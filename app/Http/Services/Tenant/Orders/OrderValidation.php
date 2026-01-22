<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Reservation\Reservation;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\WarehouseProduct;
use Exception;
use Illuminate\Support\Facades\Auth;

class OrderValidation
{
    private PettyCashBookService $s_cash_book;
    private OrderRepository $s_repository;

    public function __construct($_s_repository)
    {
        $this->s_cash_book  = new PettyCashBookService();
        $this->s_repository =   $_s_repository;
    }

    public function validationCreate(int $table_id): array
    {
        $table              =   Table::findOrFail($table_id);
        $categories         =   Category::all();
        $brands             =   Brand::all();
        $types_dish         =   UtilController::getTypesDish();
        $user               =   Auth::user();
        $igv                =   round(Company::find(1)->igv, 2);
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);
        $payment_methods    =   UtilController::getPaymentMethods();

        $petty_cash_book    =   $this->s_cash_book->waiterInCash($user->id);dd($petty_cash_book);
        if ($petty_cash_book === null) {
            throw new Exception('PERTENCES A MÁS DE UNA CAJA');
        }
        if($petty_cash_book === false){
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $vars   =   [
            'types_dish'            =>  $types_dish,
            'petty_cash_book'       =>  $petty_cash_book,
            'programming'           =>  $programming,
            'table'                 =>  $table,
            'categories'            =>  $categories,
            'brands'                =>  $brands,
            'igv'                   =>  $igv,
            'customer_formatted'    =>  $customer_formatted,
            'payment_methods'       =>  $payment_methods
        ];

        return $vars;
    }

    public function validationEdit(int $id): array
    {
        $order              =   $this->s_repository->findOrder($id);
        $order_dishes       =   $this->s_repository->getOrderDishes($id)->toArray();
        $order_products     =   $this->s_repository->getOrderProducts($id)->toArray();

        $lst_products       =   FormatController::formatLstProducts($order_products);
        $lst_dishes         =   FormatController::formatLstDishes($order_dishes);
        $lst_detail         =   array_merge($lst_products, $lst_dishes);

        $table              =   Table::findOrFail($order->table_id);
        $categories         =   Category::all();
        $brands             =   Brand::all();
        $types_dish         =   UtilController::getTypesDish();
        $user               =   Auth::user();
        $petty_cash_book    =   $this->s_cash_book->getCashBookUser($user->id);
        $igv                =   round(Company::find(1)->igv, 2);
        $customer_formatted =   FormatController::getFormatInitialCustomer($order->customer_id);

        if (Auth::user()->id != $order->creator_user_id) {
            throw new Exception("ESTE PEDIDO LE PERTENECE A OTRO MESERO");
        }

        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $vars   =   [
            'types_dish'            =>  $types_dish,
            'petty_cash_book'       =>  $petty_cash_book,
            'programming'           =>  $programming,
            'table'                 =>  $table,
            'categories'            =>  $categories,
            'brands'                =>  $brands,
            'igv'                   =>  $igv,
            'customer_formatted'    =>  $customer_formatted,

            'order'                 =>  $order,
            'order_products'        =>  $order_products,
            'order_dishes'          =>  $order_dishes,
            'lst_detail'            =>  $lst_detail,
        ];

        return $vars;
    }

    public function validationStore(array $data): array
    {
        $user               =   Auth::user();
        $petty_cash_book    =   $this->s_cash_book->getCashBookUser($user->id);
        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $table          =   Table::findOrFail($data['table_id']);
        $is_reservated  =   Reservation::where('table_id', $table->id)->where('status', 'OCUPADO')->first();
        if ($is_reservated) {
            throw new Exception("LA MESA: " . $table->name . " ESTÁ OCUPADA");
        }

        $payment_method_id  =   $data['payment_method'];
        $payment_method     =   PaymentMethod::find($payment_method_id);
        $payref_id          =   $payment_method->id;
        $payref_name        =   $payment_method->description;

        $lst_detail =   json_decode($data['lst_detail']);
        if (count($lst_detail) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO!!!");
        }

        $data['table']      =   $table;
        $data['lst_detail'] =   $lst_detail;
        $data['payref_id']  =   $payref_id;
        $data['payref_name'] =   $payref_name;

        $this->validationLstDetail($lst_detail, $programming->id);

        return $data;
    }

    public function validationUpdate(int $id, array $data)
    {
        $user               =   Auth::user();
        $order              =   $this->s_repository->findOrder($id);
        $order_products     =   $this->s_repository->getOrderProducts($id);
        $order_dishes       =   $this->s_repository->getOrderDishes($id);

        if ($user->id != $order->creator_user_id) {
            throw new Exception("ESTA PEDIDO LE PERTENECE A OTRO MESERO");
        }

        $petty_cash_book    =   $this->s_cash_book->getCashBookUser($user->id);
        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $table          =   Table::findOrFail($data['table_id']);
        $reservation    =   Reservation::where('table_id', $table->id)->first();
        if ($reservation->status !== 'OCUPADO') {
            throw new Exception("LA MESA: " . $table->name . " HA  CAMBIADO SU RESERVA A: " . $reservation->status);
        }

        $lst_detail =   json_decode($data['lst_detail']);
        if (count($lst_detail) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO!!!");
        }

        $data['table']              =   $table;
        $data['lst_detail']         =   $lst_detail;
        $data['order_products']     =   $order_products;
        $data['order_dishes']       =   $order_dishes;
        $data['programming_id']     =   $programming->id;

        $this->validationLstDetailUpdate($data);
        return $data;
    }

    public function validationLstDetail(array $lst_detail, int $programming_id)
    {
        foreach ($lst_detail as $item) {

            if ($item->type_item === 'PLATO') {
                $item_bd = ProgrammingDetail::where('programming_id', $programming_id)->where('dish_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN LA PROGRAMACIÓN");
                }
                if ($item_bd->stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $item_bd->stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }

            if ($item->type_item === 'PRODUCTO') {
                $item_bd = WarehouseProduct::where('warehouse_id', 1)->where('product_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN EL ALMACÉN");
                }
                if ($item_bd->stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $item_bd->stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }
        }
    }

    public function validationLstDetailUpdate(array $data)
    {
        $lst_detail     =   $data['lst_detail'];
        $programming_id =   $data['programming_id'];
        $order_products =   $data['order_products'];
        $order_dishes   =   $data['order_dishes'];

        foreach ($lst_detail as $item) {

            if ($item->type_item === 'PLATO') {
                $item_bd = ProgrammingDetail::where('programming_id', $programming_id)->where('dish_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN LA PROGRAMACIÓN");
                }

                $item_preview       =   $order_dishes->where('dish_id', $item->id)->first();
                $quantity_preview   =   $item_preview ? $item_preview->quantity : 0;
                $stock              =   (int)$item_bd->stock + $quantity_preview;
                if ($stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }

            if ($item->type_item === 'PRODUCTO') {
                $item_bd = WarehouseProduct::where('warehouse_id', 1)->where('product_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN EL ALMACÉN");
                }


                $quantity_preview   =   (int)$order_products->where('product_id', $item->id)->first()->quantity;
                $stock              =   (int)$item_bd->stock + $quantity_preview;
                if ($stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }
        }
    }
}
