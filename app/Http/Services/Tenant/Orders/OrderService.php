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
        $data   =   $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);

        $order  =   $this->s_repository->store($dto);

        $collect_detail =   collect($data['lst_detail']);
        $lst_dishes     =   $collect_detail->where('type_item', 'PLATO')->toArray();
        $lst_products   =   $collect_detail->where('type_item', 'PRODUCTO')->toArray();
        $dto_odish      =   $this->s_dto->getDtoOrderDish($lst_dishes, $order->id);
        $dto_oproduct   =   $this->s_dto->getDtoOrderProduct($lst_products, $order->id);

        $this->s_repository->storeOrderProduct($dto_oproduct);
        $this->s_repository->storeOrderDish($dto_odish);

        $this->s_reservation->store($order);

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

        $dto            =   $this->s_dto->getDtoStore($data);
        $order          =   $this->s_repository->update($id, $dto);

        $collect_detail =   collect($data['lst_detail']);
        $lst_dishes     =   $collect_detail->where('type_item', 'PLATO')->toArray();
        $lst_products   =   $collect_detail->where('type_item', 'PRODUCTO')->toArray();
        $dto_odish      =   $this->s_dto->getDtoOrderDish($lst_dishes, $order->id);
        $dto_oproduct   =   $this->s_dto->getDtoOrderProduct($lst_products, $order->id);


        $this->operationStock($dto_odish, $dto_oproduct, $id);
        $this->s_repository->cancelOrderDishes($id);
        $this->s_repository->cancelOrderProducts($id);

        $this->s_repository->storeOrderProduct($dto_oproduct);
        $this->s_repository->storeOrderDish($dto_odish);

        $s_kardex   =   new KardexService();
        $s_kardex->updateFromOrder($order);

        return $order;
    }

    public function operationStock(array $lst_dishes, array $lst_products, int $order_id)
    {
        $this->operationStockDish($lst_dishes, $order_id);
        $this->operationStockProduct($lst_products, $order_id);
    }

    public function operationStockDish($lst_dishes, $order_id)
    {
        $dishes_ant     =   $this->s_repository->getOrderDishes($order_id);
        $this->s_programming->increaseLstStock($dishes_ant->toArray());

        $this->s_programming->decreaseLstStock($lst_dishes);
    }

    public function operationStockProduct($lst_products, $order_id)
    {
        $products_ant     =   $this->s_repository->getOrderProducts($order_id);
        $this->s_pct->increaseLstStock($products_ant->toArray());

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
