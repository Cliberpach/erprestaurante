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
        $data           =   $this->s_validation->validationStore($data);
        $data['mode']   =   'STORE';
        $dto            =   $this->s_dto->getDtoStore($data);
        $order          =   $this->s_repository->store($dto);

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

        //======== OPERAR PRODUCTOS =======
        $this->operationProducts($data, $id);
        $this->operationDishes($data, $id);

        $s_kardex   =   new KardexService();
        $s_kardex->updateFromOrder($order);

        return $order;
    }

    public function operationProducts(array $data, int $id)
    {
        $productsBD     =   $this->s_repository->getOrderProducts($id);
        $lst_detail     =   collect($data['lst_detail'])->where('type_item', 'PRODUCTO');
        $lst_news       =   $lst_detail->whereNull('order_detail_id')->values();
        $lst_olds_front =   $lst_detail->whereNotNull('order_detail_id')->keyBy('order_detail_id');

        $lst_kept       =   $productsBD->whereIn('id', $lst_olds_front->keys())->values();
        $lst_deleted    =   $productsBD->whereNotIn('id', $lst_olds_front->keys())->values();

        $lst_olds_bd = $lst_kept
            ->map(function ($item) use ($lst_olds_front) {

                $item     = (object) $item->getAttributes();
                $frontQty = (int) $lst_olds_front[$item->id]->quantity;
                $bdQty    = (int) $item->quantity;

                $data = $item;

                $data->quantity_front    = $frontQty;
                $data->quantity_diff     = $frontQty - $bdQty;
                $data->quantity_changed = $frontQty !== $bdQty;
                $data->quantity_direction = match (true) {
                    $frontQty > $bdQty => 'UP',
                    $frontQty < $bdQty => 'DOWN',
                    default            => 'SAME',
                };

                return $data;
            })
            ->values()
            ->toArray();

        //======== OPERAR NUEVOS ========
        $lst_news_products  =   $lst_news->toArray();
        $dto_oproduct_news  =   $this->s_dto->getDtoOrderProduct($lst_news_products, $id, 'UPDATE_NEW');
        $this->s_repository->storeOrderProduct($dto_oproduct_news);

        $lst_news_products = array_map(fn($i) => (object)$i, $dto_oproduct_news);
        $this->s_pct->decreaseLstStock($lst_news_products);

        //====== MANTENIDOS =======
        $lst_olds = array_map(function ($item) {
            $data = (array) $item;
            $data['order_detail_id'] = $data['id'];
            $data['id']              = $data['product_id'];
            unset($data['product_id']);
            return (object) $data;
        }, $lst_olds_bd);

        $dto_oproduct_olds = $this->s_dto->getDtoOrderProduct($lst_olds, $id, 'UPDATE_OLD');
        $this->s_repository->updateOrderProduct($dto_oproduct_olds);

        $filter = fn($dir) => array_values(
            array_map(
                fn($item) => (object)[
                    'product_id'   => $item->product_id,
                    'warehouse_id' => $item->warehouse_id ?? 1,
                    'quantity'     => abs($item->quantity_diff),
                ],
                array_filter(
                    $lst_olds_bd,
                    fn($item) => $item->quantity_direction === $dir
                )
            )
        );

        $lst_up_productos   = $filter('UP');
        $lst_down_productos = $filter('DOWN');
        $lst_same_products = $filter('SAME');

        $this->s_pct->decreaseLstStock($lst_down_productos);
        $this->s_pct->increaseLstStock($lst_up_productos);

        // ===== ELIMINADOS =====
        $lst_deleted_products = $lst_deleted
            ->map(fn($item) => (object) $item->getAttributes())
            ->all();

        $this->s_pct->increaseLstStock($lst_deleted_products);

        $lst_deleted_products_ids = array_column($lst_deleted_products, 'id');
        $this->s_repository->cancelOrderDetailsByIds([], $lst_deleted_products_ids);
    }

    public function operationDishes(array $data, int $id)
    {
        $dishesBD       =   $this->s_repository->getOrderDishes($id);
        $lst_detail     =   collect($data['lst_detail'])->where('type_item', 'PLATO');
        $lst_news       =   $lst_detail->whereNull('order_detail_id')->values();
        $lst_olds_front =   $lst_detail->whereNotNull('order_detail_id')->keyBy('order_detail_id');

        $lst_kept       =   $dishesBD->whereIn('id', $lst_olds_front->keys())->values();
        $lst_deleted    =   $dishesBD->whereNotIn('id', $lst_olds_front->keys())->values();

        $lst_olds_bd = $lst_kept
            ->map(function ($item) use ($lst_olds_front) {

                $item     = (object) $item->getAttributes();
                $frontQty = (int) $lst_olds_front[$item->id]->quantity;
                $bdQty    = (int) $item->quantity;

                $data = $item;

                $data->quantity_front    = $frontQty;
                $data->quantity_diff     = $frontQty - $bdQty;
                $data->quantity_changed = $frontQty !== $bdQty;
                $data->quantity_direction = match (true) {
                    $frontQty > $bdQty => 'UP',
                    $frontQty < $bdQty => 'DOWN',
                    default            => 'SAME',
                };

                return $data;
            })
            ->values()
            ->toArray();

        //======== OPERAR NUEVOS ========
        $lst_news_dishes    =   $lst_news->toArray();
        $dto_odishes_news   =   $this->s_dto->getDtoOrderDish($lst_news_dishes, $id, 'UPDATE_NEW');
        $this->s_repository->storeOrderDish($dto_odishes_news);

        $lst_news_dishes = array_map(fn($i) => (object)$i, $dto_odishes_news);
        $this->s_programming->decreaseLstStock($lst_news_dishes);

        //====== MANTENIDOS =======
        $lst_olds = array_map(function ($item) {
            $data = (array) $item;
            $data['order_detail_id'] = $data['id'];
            $data['id']              = $data['dish_id'];
            unset($data['dish_id']);
            return (object) $data;
        }, $lst_olds_bd);

        $dto_oproduct_olds = $this->s_dto->getDtoOrderDish($lst_olds, $id, 'UPDATE_OLD');
        $this->s_repository->updateOrderDish($dto_oproduct_olds);

        $filter = fn($dir) => array_values(
            array_map(
                fn($item) => (object)[
                    'dish_id'   => $item->dish_id,
                    'programming_id' => $item->programming_id ?? 1,
                    'quantity'     => abs($item->quantity_diff),
                ],
                array_filter(
                    $lst_olds_bd,
                    fn($item) => $item->quantity_direction === $dir
                )
            )
        );

        $lst_up_dishes   = $filter('UP');
        $lst_down_dishes = $filter('DOWN');
        $lst_same_products = $filter('SAME');

        $this->s_programming->decreaseLstStock($lst_down_dishes);
        $this->s_programming->increaseLstStock($lst_up_dishes);

        // ===== ELIMINADOS =====
        $lst_deleted_dishes = $lst_deleted
            ->map(fn($item) => (object) $item->getAttributes())
            ->all();

        $this->s_programming->increaseLstStock($lst_deleted_dishes);

        $lst_deleted_dishes_ids = array_column($lst_deleted_dishes, 'id');
        $this->s_repository->cancelOrderDetailsByIds($lst_deleted_dishes_ids, []);
    }

    public function getOrderDetail(int $order_id)
    {
        $order_dishes       =   $this->s_repository->getOrderDishes($order_id)->toArray();
        $order_products     =   $this->s_repository->getOrderProducts($order_id)->toArray();

        $details        =   FormatController::formatDetailOrder($order_products, $order_dishes);
        return $details;
    }

    public function getOrderDetailCanceled(int $order_id)
    {
        $dishes_canceled    =   $this->s_repository->getOrderDishesCanceled($order_id)->toArray();
        $products_canceled  =   $this->s_repository->getOrderProductsCanceled($order_id)->toArray();

        $details    =   FormatController::formatDetailOrder($products_canceled, $dishes_canceled);
        return $details;
    }

    public function getOrderDishes(int $order_id)
    {
        return $this->s_repository->getOrderDishes($order_id);
    }

    public function getOrderProducts(int $order_id)
    {
        return $this->s_repository->getOrderProducts($order_id);
    }

    public function getOrderDishesCanceled(int $order_id)
    {
        return $this->s_repository->getOrderDishesCanceled($order_id);
    }

    public function getOrderProductsCanceled(int $order_id)
    {
        return $this->s_repository->getOrderProductsCanceled($order_id);
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

    public function preAccount(int $id): Order
    {
        $order = $this->s_repository->setPendingPrint($id, 'SI');
        return $order;
    }

    public function changeTable(array $data): Order
    {
        $order_id       =   $data['order_id'];
        $table_selected =   $data['table_selected'];
        $order          =   $this->s_repository->findOrder($data['order_id']);
        $data['order']  =   $order;
        $this->s_validation->validationChangeTable($data);
        $table_old      =   $order->table_id;
        $order          =   $this->s_repository->changeTable($order_id, $table_selected);
        $this->s_reservation->changeTable($order_id, $table_selected, $table_old);
        return $order;
    }

    public function destroy(array $data, int $id): Order
    {
        $order  =   $this->s_repository->findOrder($id);
        $data['order']  =   $order;
        $this->s_validation->validationDestroyOrder($data);

        $products = $this->s_repository->getOrderProducts($id)
            ->map(fn($item) => (object) $item->toArray())
            ->toArray();

        $dishes = $this->s_repository->getOrderDishes($id)
            ->map(fn($item) => (object) $item->toArray())
            ->toArray();

        $this->s_repository->deleteOrder($id);
        $this->s_reservation->deleteFromOrder($id, $order->table_id);

        $this->s_pct->increaseLstStock($products);
        $this->s_programming->increaseLstStock($dishes);

        $s_kardex   =   new KardexService();
        $s_kardex->updateFromOrder($order);

        return $order;
    }

    public function addPay(array $data, int $id): Order
    {
        $order  =   $this->s_repository->findOrder($id);
        $data['order']  =   $order;
        $this->s_validation->validationAddPay($data);

        $dto    =   $this->s_dto->getDtoAddPay($data);

        $payref_img_name_prev  =   $order->payref_img_name;

        //========= SAVE VOUCHER ===========
        if (isset($data['voucher'])) {
            if ($payref_img_name_prev) {
                UtilController::deleteFile($order->payref_img_url);
            }
            UtilController::saveImg($data['voucher'], $dto['payref_img_name'], 'orders/payrefs/');
        }

        $order  =   $this->s_repository->update($id, $dto);

        return $order;
    }
}
