<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Inventory\Kardex\KardexService;
use App\Http\Services\Tenant\Inventory\WarehouseProduct\WarehouseProductService;
use App\Http\Services\Tenant\Reservation\ReservationService;
use App\Http\Services\Tenant\Supply\Programming\ProgrammingService;
use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;

class OrderService
{
    private OrderValidation $s_validation;
    private OrderDto $s_dto;
    private OrderRepository $s_repository;
    private ReservationService $s_reservation;
    private WarehouseProductService $s_pct;
    private ProgrammingService $s_programming;

    public function __construct()
    {
        $this->s_dto            =   new OrderDto();
        $this->s_repository     =   new OrderRepository();
        $this->s_validation     =   new OrderValidation($this->s_repository);
        $this->s_reservation    =   new ReservationService();
        $this->s_pct            =   new WarehouseProductService();
        $this->s_programming    =   new ProgrammingService();
    }

    public function create(int $table_id): View
    {
        $vars   =   $this->s_validation->validationCreate($table_id);

        return view('orders.create', $vars);
    }

    public function store(array $data): Order
    {
        $data['mode']   =   'STORE';
        $data   =   $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);

        $order  =   $this->s_repository->store($dto);

        $collect_detail =   collect($data['lst_detail']);
        $lst_dishes     =   $collect_detail->where('type_item', 'PLATO')->toArray();
        $lst_products   =   $collect_detail->where('type_item', 'PRODUCTO')->toArray();
        $dto_odish      =   $this->s_dto->getDtoOrderDish($lst_dishes, $order->id, 'STORE');
        $dto_oproduct   =   $this->s_dto->getDtoOrderProduct($lst_products, $order->id, 'STORE');

        $this->s_repository->storeOrderProduct($dto_oproduct);
        $this->s_repository->storeOrderDish($dto_odish);

        $this->s_reservation->store($order);

        $dto_odish    = array_map(fn($item) => (object) $item, $dto_odish);
        $dto_oproduct = array_map(fn($item) => (object) $item, $dto_oproduct);
        $this->s_pct->decreaseLstStock($dto_oproduct);
        $this->s_programming->decreaseLstStock($dto_odish);

        //========= SAVE VOUCHER ===========
        if (isset($data['voucher'])) {
            UtilController::saveImg($data['voucher'], $order->payref_img_name, 'orders/payrefs/');
        }

        if (!empty($lst_products)) {
            $s_kardex   =   new KardexService();
            $s_kardex->storeFromOrder($order);
        }

        return $order;
    }

    public function getOrderTable(int $table_id)
    {
        $item   =   $this->s_repository->getOrderTable($table_id);
        return $item;
    }

    public function edit($id): View
    {
        $vars           =   $this->s_validation->validationEdit($id);
        return view('orders.edit', $vars);
    }

    public function update(int $id, array $data): Order
    {
        $data           =   $this->s_validation->validationUpdate($id, $data);
        $data['mode']   =   "UPDATE";
        $dto            =   $this->s_dto->getDtoStore($data);
        $order          =   $this->s_repository->update($id, $dto);

        $detailsBD      =   collect($this->s_repository->getDetails($id));
        $lst_detail     =   collect($data['lst_detail']);
        $lst_news       =   $lst_detail->whereNull('order_detail_id')->values();
        $lst_olds_front =   $lst_detail->whereNotNull('order_detail_id')->keyBy('order_detail_id');
        $lst_kept       =   $detailsBD->whereIn('id', $lst_olds_front->keys())->values();
        $lst_deleted    =   $detailsBD->whereNotIn('id', $lst_olds_front->keys())->values();

        $lst_olds_bd = $lst_kept
            ->map(function ($item) use ($lst_olds_front) {

                $item     = (object) $item;
                $frontQty = (int) $lst_olds_front[$item->id]['quantity'];
                $bdQty    = (int) $item->quantity;

                $data = (array) $item;

                $data['quantity_front']     = $frontQty;
                $data['quantity_diff']      = $frontQty - $bdQty;
                $data['quantity_changed']   = $frontQty !== $bdQty;
                $data['quantity_direction'] = match (true) {
                    $frontQty > $bdQty => 'UP',
                    $frontQty < $bdQty => 'DOWN',
                    default            => 'SAME',
                };

                return $data;
            })
            ->values()
            ->toArray();

        //======== OPERAR NUEVOS ========
        $lst_news_dishes    =   $lst_news->where('type_item', 'PLATO')->toArray();
        $lst_news_products  =   $lst_news->where('type_item', 'PRODUCTO')->toArray();

        $dto_odish_news     =   $this->s_dto->getDtoOrderDish($lst_news_dishes, $order->id, 'UPDATE_NEW');
        $dto_oproduct_news  =   $this->s_dto->getDtoOrderProduct($lst_news_products, $order->id, 'UPDATE_NEW');

        $this->s_repository->storeOrderProduct($dto_oproduct_news);
        $this->s_repository->storeOrderDish($dto_odish_news);
        $this->s_programming->decreaseLstStock($dto_odish_news);
        $this->s_pct->decreaseLstStock($dto_oproduct_news);

        //====== MANTENIDOS =======
        $lst_olds_dishes   = array_map(fn($i) => (object)$i, array_filter($lst_olds_bd, fn($i) => $i['type_item'] === 'PLATO'));
        $lst_olds_products = array_map(fn($i) => (object)$i, array_filter($lst_olds_bd, fn($i) => $i['type_item'] === 'PRODUCTO'));

        $dto_odish_olds    = $this->s_dto->getDtoOrderDish($lst_olds_dishes, $order->id, 'UPDATE_OLD');
        $dto_oproduct_olds = $this->s_dto->getDtoOrderProduct($lst_olds_products, $order->id, 'UPDATE_OLD');

        $this->s_repository->updateOrderDish($dto_odish_olds);
        $this->s_repository->updateOrderProduct($dto_oproduct_olds);

        $filter = fn($dir, $type) => array_values(
            array_filter(
                array_map(
                    fn($item) => match ($item['type_item']) {
                        'PRODUCTO' => [
                            'product_id'   => $item['id'],
                            'warehouse_id' => $item['warehouse_id'] ?? 1,
                            'quantity'     => abs($item['quantity_diff']),
                        ],
                        'PLATO' => [
                            'programming_id' => $item['programming_id'],
                            'dish_id'        => $item['id'],
                            'quantity'       => abs($item['quantity_diff']),
                        ],
                        default => null,
                    },
                    array_filter(
                        $lst_olds_bd,
                        fn($item) => $item['quantity_direction'] === $dir && $item['type_item'] === $type
                    )
                ),
                fn($i) => !is_null($i)
            )
        );

        $lst_up_platos      = $filter('UP', 'PLATO');
        $lst_up_productos   = $filter('UP', 'PRODUCTO');
        $lst_down_platos    = $filter('DOWN', 'PLATO');
        $lst_down_productos = $filter('DOWN', 'PRODUCTO');

        $this->s_pct->decreaseLstStock($lst_down_productos);
        $this->s_pct->increaseLstStock($lst_up_productos);
        $this->s_programming->decreaseLstStock($lst_down_platos);
        $this->s_programming->increaseLstStock($lst_up_platos);

        // ===== ELIMINADOS =====
        $lst_deleted_dishes   = $lst_deleted->where('type_item', 'PLATO')->map(fn($i) => (array)$i)->values()->toArray();
        $lst_deleted_products = $lst_deleted->where('type_item', 'PRODUCTO')->map(fn($i) => (array)$i)->values()->toArray();

        $this->s_pct->increaseLstStock(array_map(fn($i) => (object)[
            'product_id' => $i['id'],
            'warehouse_id' => $i['warehouse_id'] ?? 1,
            'quantity' => (int)$i['quantity'],
        ], $lst_deleted_products));

        $this->s_programming->increaseLstStock(array_map(fn($i) => (object)[
            'programming_id' => $i['programming_id'],
            'dish_id' => $i['id'],
            'quantity' => (int)$i['quantity'],
        ], $lst_deleted_dishes));

        $lst_deleted_dishes_ids   = array_column($lst_deleted_dishes, 'id');
        $lst_deleted_products_ids = array_column($lst_deleted_products, 'id');

        $this->s_repository->cancelOrderDetailsByIds($lst_deleted_dishes_ids, $lst_deleted_products_ids);

        $s_kardex   =   new KardexService();
        $s_kardex->updateFromOrder($order);

        return $order;
    }

    public function operationStock(array $lst_dishes, array $lst_products)
    {
        $this->operationStockDish($lst_dishes);
        $this->operationStockProduct($lst_products);
    }

    public function operationStockDish($lst_dishes)
    {
        $this->s_programming->decreaseLstStock($lst_dishes);
    }

    public function operationStockProduct($lst_products)
    {
        $this->s_pct->decreaseLstStock($lst_products);
    }

    public function getOrderDetail(int $order_id)
    {
        $order_dishes       =   $this->s_repository->getOrderDishes($order_id)->toArray();
        $order_products     =   $this->s_repository->getOrderProducts($order_id)->toArray();

        $lst_products       =   FormatController::formatLstProducts($order_products);
        $lst_dishes         =   FormatController::formatLstDishes($order_dishes);
        $lst_detail         =   array_merge($lst_products, $lst_dishes);
        return $lst_detail;
    }

    public function getOrderDishes(int $order_id)
    {
        return $this->s_repository->getOrderDishes($order_id);
    }

    public function getOrderProducts(int $order_id)
    {
        return $this->s_repository->getOrderProducts($order_id);
    }

    public function setStatusInvoice(int $id, string $status, $invoice)
    {
        $this->s_repository->setStatusInvoice($id, $status, $invoice);
        $this->s_reservation->setStatusByOrder($id, 'FINALIZADO');
    }

    public function getDetails(int $id)
    {
        return $this->s_repository->getDetails($id);
    }
}
