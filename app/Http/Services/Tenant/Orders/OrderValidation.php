<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Http\Services\Tenant\Supply\Table\TableService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Reservation\Reservation;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\WarehouseProduct;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $petty_cash_book    =   $this->s_cash_book->waiterInCash($user->id);
        if ($petty_cash_book === null) {
            throw new Exception('PERTENCES A MÁS DE UNA CAJA');
        }
        if ($petty_cash_book === false) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $vars_mdlcustomer   =   UtilController::getVarsMdlCustomer();

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

        $vars   =   array_merge($vars, $vars_mdlcustomer);

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
        $igv                =   round(Company::find(1)->igv, 2);
        $customer_formatted =   FormatController::getFormatInitialCustomer($order->customer_id);
        $petty_cash_book    =   $this->s_cash_book->getCashBookWaiter($user->id);
        $programming        =   null;
        if ($petty_cash_book) {
            $programming        =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        }

        $config_delete      =   Configuration::findOrFail(2)->status;

        if ($order->status !== 'ACTIVO') {
            throw new Exception('EL PEDIDO SE ENCUENTRA CON ESTADO: ' . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception('EL PEDIDO YA FUE FACTURADO');
        }

        if (!$user->hasRole('MESERO')) {
            throw new Exception('NO TIENES PERMISOS DE MESERO PARA REALIZAR ESTA ACCIÓN!!!');
        }

        if (Auth::user()->id != $order->creator_user_id) {
            throw new Exception("ESTE PEDIDO LE PERTENECE A OTRO MESERO");
        }

        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }


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
            'config_delete'         =>  $config_delete
        ];

        return $vars;
    }

    public function validationStore(array $data): array
    {
        $user               =   Auth::user();
        if (!$user->hasRole('MESERO')) {
            throw new Exception('NO TIENES PERMISOS DE MESERO PARA REALIZAR ESTA ACCIÓN!!!');
        }

        $petty_cash_book    =   $this->s_cash_book->getCashBookWaiter($user->id);
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
        $payref_id          =   $payment_method?->id;
        $payref_name        =   $payment_method?->description;

        $lst_detail =   json_decode($data['lst_detail']);
        if (count($lst_detail) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO!!!");
        }

        $data['table']              =   $table;
        $data['lst_detail']         =   $lst_detail;
        $data['payref_id']          =   $payref_id;
        $data['payref_name']        =   $payref_name;
        $data['petty_cash_book']    =   $petty_cash_book;

        $this->validationLstDetailStore($lst_detail, $programming->id, 1);

        return $data;
    }

    public function validationUpdate(int $id, array $data)
    {
        $user               =   Auth::user();
        $order              =   $this->s_repository->findOrder($id);
        $order_products     =   $this->s_repository->getOrderProducts($id);
        $order_dishes       =   $this->s_repository->getOrderDishes($id);

        if (!$user->hasRole('MESERO')) {
            throw new Exception('NO TIENES PERMISOS DE MESERO PARA REALIZAR ESTA ACCIÓN!!!');
        }
        if ($user->id != $order->creator_user_id) {
            throw new Exception("ESTA PEDIDO LE PERTENECE A OTRO MESERO");
        }

        if ($order->status !== 'ACTIVO') {
            throw new Exception('EL PEDIDO SE ENCUENTRA CON ESTADO: ' . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception('EL PEDIDO YA FUE FACTURADO');
        }

        $petty_cash_book    =   $this->s_cash_book->getCashBookWaiter($user->id);
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
        $reservation    =   Reservation::where('table_id', $table->id)->where('order_id', $order->id)->first();
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
        $data['petty_cash_book']    =   $petty_cash_book;

        $this->validationLstDetailUpdate($data);
        return $data;
    }

    public function validationLstDetailStore(array $lst_detail, int $programming_id, int $warehouse_id)
    {
        $this->validationLstQuantities($lst_detail);
        $lst_products   =   collect($lst_detail)->where('type_item', 'PRODUCTO')->values()->toArray();
        $lst_dishes   =   collect($lst_detail)->where('type_item', 'PLATO')->values()->toArray();

        $this->validationProductsStore($lst_products, $warehouse_id);
        $this->validationDishesStore($lst_dishes, $programming_id);
    }

    public function validationProductsStore(array $lst_products, int $warehouse_id)
    {
        $lst_totales = collect($lst_products)
            ->map(function ($item) {
                $item->product_id = $item->id;
                return $item;
            })
            ->groupBy('product_id')
            ->map(fn($items) => $items->sum('quantity'));


        $warehouses = WarehouseProduct::where('warehouse_id', $warehouse_id)
            ->whereIn('product_id', $lst_totales->keys())
            ->get()
            ->keyBy('product_id');

        foreach ($lst_totales as $product_id => $quantity_total) {
            $warehouse_product  =   $warehouses[$product_id] ?? null;
            if (!$warehouse_product) {
                throw new Exception(
                    "Producto {$product_id} no existe en el almacén {$warehouse_id}"
                );
            }

            $stock  =   (int)$warehouse_product->stock;
            if ((int)$quantity_total > $stock) {
                throw new Exception(
                    "Producto {$product_id} stock ({$stock}) insuficiente, cantidad requerida: {$quantity_total}"
                );
            }
        }
    }

    public function validationDishesStore($lst_dishes, $programming_id)
    {
        $lst_totales = collect($lst_dishes)
            ->map(function ($item) {
                $item->dish_id = $item->id;
                return $item;
            })
            ->groupBy('dish_id')
            ->map(fn($items) => $items->sum('quantity'));


        $programmings = ProgrammingDetail::where('programming_id', $programming_id)
            ->whereIn('dish_id', $lst_totales->keys())
            ->get()
            ->keyBy('dish_id');

        foreach ($lst_totales as $dish_id => $quantity_total) {
            $programming  =   $programmings[$dish_id] ?? null;
            if (!$programming) {
                throw new Exception(
                    "Plato {$dish_id} no existe en la programación {$programming_id}"
                );
            }

            $stock  =   (int)$programming->stock;
            if ((int)$quantity_total > $stock) {
                throw new Exception(
                    "Plato {$dish_id} stock ({$stock}) insuficiente, cantidad requerida: {$quantity_total}"
                );
            }
        }
    }

    public function validationLstQuantities(array $lst_detail)
    {
        foreach ($lst_detail as $item) {
            $validator = Validator::make(
                ['quantity' => $item->quantity],
                [
                    'quantity' => [
                        'required',
                        'numeric',
                        'regex:/^\d{1,16}(\.\d{1,6})?$/',
                    ],
                ],
                [
                    'quantity.required' => 'La cantidad es obligatoria.',
                    'quantity.numeric'  => 'La cantidad debe ser un número válido.',
                    'quantity.regex'    => 'La cantidad debe tener máximo 16 dígitos y hasta 6 decimales.',
                ]
            );

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first('quantity'));
            }
        }
    }

    public function validationLstDetailUpdate(array $data)
    {
        $lst_detail     =   $data['lst_detail'];
        $programming_id =   $data['programming_id'];
        $order_dishes   =   $data['order_dishes'];
        $order_products =   $data['order_products'];

        $this->validationLstQuantities($lst_detail);
        $this->validationDishesUpdate($lst_detail, $order_dishes, $programming_id);
        $this->validationProductsUpdate($lst_detail, $order_products, 1);
    }

    public function validationDishesUpdate($lst_detail, $order_dishes, $programming_id)
    {
        $lst_totales = collect($lst_detail)
            ->where('type_item', 'PLATO')
            ->map(function ($item) {
                $item->dish_id = $item->id;
                return $item;
            })
            ->groupBy('dish_id')
            ->map(fn($items) => $items->sum('quantity'));

        $lst_old_totales = collect($order_dishes)
            ->groupBy('dish_id')
            ->map(fn($items) => $items->sum('quantity'));

        $lst_delta = $lst_totales->map(function ($newQty, $dishId) use ($lst_old_totales) {
            $oldQty = $lst_old_totales[$dishId] ?? 0;
            return $newQty - $oldQty;
        })->filter(fn($delta) => $delta > 0);


        $programmings = ProgrammingDetail::where('programming_id', $programming_id)
            ->whereIn('dish_id', $lst_delta->keys())
            ->get()
            ->keyBy('dish_id');
        foreach ($lst_delta as $dish_id => $delta_quantity) {

            $item_programming = $programmings[$dish_id] ?? null;

            if (!$item_programming) {
                throw new Exception("PLATO {$dish_id} NO EXISTE EN LA PROGRAMACIÓN");
            }

            if ($delta_quantity > $item_programming->stock) {
                $item_programming->stock    =   (int)$item_programming->stock;
                throw new Exception(
                    "STOCK INSUFICIENTE PARA PLATO {$dish_id}. " .
                        "Disponible: {$item_programming->stock}, requerido: {$delta_quantity}"
                );
            }
        }
    }

    public function validationProductsUpdate($lst_detail, $order_products, $warehouse_id)
    {
        $lst_totales = collect($lst_detail)
            ->where('type_item', 'PRODUCTO')
            ->map(function ($item) {
                $item->product_id = $item->id;
                return $item;
            })
            ->groupBy('product_id')
            ->map(fn($items) => $items->sum('quantity'));

        $lst_old_totales = collect($order_products)
            ->groupBy('product_id')
            ->map(fn($items) => $items->sum('quantity'));

        $lst_delta = $lst_totales->map(function ($newQty, $productId) use ($lst_old_totales) {
            $oldQty = $lst_old_totales[$productId] ?? 0;
            return $newQty - $oldQty;
        })->filter(fn($delta) => $delta > 0);

        $warehouses = WarehouseProduct::where('warehouse_id', $warehouse_id)
            ->whereIn('product_id', $lst_delta->keys())
            ->get()
            ->keyBy('product_id');

        foreach ($lst_delta as $product_id => $delta_quantity) {

            $item_warehouse = $warehouses[$product_id] ?? null;

            if (!$item_warehouse) {
                throw new Exception("PRODUCTO {$product_id} NO EXISTE EN EL ALMACÉN");
            }

            if ($delta_quantity > $item_warehouse->stock) {
                $item_warehouse->stock  =   (int)$item_warehouse->stock;
                throw new Exception(
                    "STOCK INSUFICIENTE PARA PRODUCTO {$product_id}. " .
                        "Disponible: {$item_warehouse->stock}, requerido: {$delta_quantity}"
                );
            }
        }
    }

    public function validationChangeTable(array $data)
    {
        $user   =   Auth::user();
        $order  =   $data['order'];
        if (!$user->hasRole('MESERO')) {
            throw new Exception('NO TIENES PERMISOS DE MESERO PARA REALIZAR ESTA ACCIÓN!!!');
        }
        if ($user->id != $order->creator_user_id) {
            throw new Exception("ESTA PEDIDO LE PERTENECE A OTRO MESERO");
        }
        if ($order->status !== 'ACTIVO') {
            throw new Exception('NO SE PUEDE CAMBIAR DE MESA, PEDIDO CON ESTADO: ' . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception('NO SE PUEDE CAMBIAR DE MESA, PEDIDO YA FACTURADO');
        }

        $s_table       =   new TableService();
        $isNotFree     =   $s_table->isNotFree($data['table_selected']);
        if ($isNotFree) {
            throw new Exception("LA MESA NUEVA ESTÁ OCUPADA POR LA RESERVA: " . $isNotFree->code);
        }
    }

    public function validationDestroyOrder(array $data)
    {
        $user   =   Auth::user();
        $order  =   $data['order'];
        if (!$user->hasRole('MESERO')) {
            throw new Exception('NO TIENES PERMISOS DE MESERO PARA REALIZAR ESTA ACCIÓN!!!');
        }
        if ($user->id != $order->creator_user_id) {
            throw new Exception("ESTA PEDIDO LE PERTENECE A OTRO MESERO");
        }
        if ($order->status !== 'ACTIVO') {
            throw new Exception('NO SE PUEDE ELIMINAR, PEDIDO CON ESTADO: ' . $order->status);
        }
        if ($order->status_invoice !== 'NO FACTURADO') {
            throw new Exception('NO SE PUEDE ELIMINAR, PEDIDO YA FACTURADO');
        }

        $password_bd    =   Configuration::findOrFail(2)->property;
        $password       =   trim($data['password_delete_order']);

        if ($password_bd !== $password) {
            throw new Exception("Contraseña incorrecta");
        }
    }
}
