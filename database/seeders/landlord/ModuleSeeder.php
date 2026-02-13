<?php

namespace Database\Seeders\landlord;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\ModuleChild;
use App\Models\ModuleGrandChild;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDashboard();
        $this->seedCaja();
        $this->seedVentas();
        $this->seedAbastecimiento();
        $this->seedCuentas();
        $this->seedInventario();
        $this->seedMostradorMesero();
        $this->seedMostradorCajero();
        $this->seedCompras();
        $this->seedReportes();
        $this->seedMantenimiento();
        $this->seedConsultas();
    }

    /* =========================
        Helpers
    ========================== */

    private function createModule(string $description, int $render_order, string $icon, int $order = 1, ?string $show = null): Module
    {
        return Module::create(compact('description', 'order', 'show', 'render_order'));
    }

    private function createChild(Module $module, array $data): ModuleChild
    {
        return ModuleChild::create(array_merge([
            'module_id' => $module->id,
            'order' => 2,
        ], $data));
    }

    private function createGrandChild(ModuleChild $child, array $data): void
    {
        ModuleGrandChild::create(array_merge([
            'module_child_id' => $child->id,
            'order' => 3,
        ], $data));
    }

    private function seedDashboard(): void
    {
        $module = $this->createModule('Dashboard', 1, 'dashboard-graph-analytics-report-svgrepo-com.svg');

        $this->createChild($module, [
            'description' => 'Dashboard',
            'route_name' => 'dashboard.dashboard.index',
        ]);
    }


    /* =========================
        Seeds por módulo
    ========================== */

    private function seedCaja(): void
    {
        $module = $this->createModule('Cajas', 1, 'cashier_machine_cash_register_pos_icon_225168.svg');

        $this->createChild($module, [
            'description' => 'Caja',
            'route_name' => 'cajas.cajas.index',
        ]);

        $this->createChild($module, [
            'description' => 'Apertura/Cierre',
            'route_name' => 'cajas.apertura_cierre.index',
        ]);

        $this->createChild($module, [
            'description' => 'Egresos',
            'route_name' => 'cajas.egresos.index',
        ]);
    }

    private function seedVentas(): void
    {
        $module = $this->createModule('Ventas', 2, 'percentage-sales-svgrepo-com.svg');

        $this->createChild($module, [
            'description' => 'Comprobante Venta',
            'route_name' => 'ventas.comprobante_venta.index',
        ]);

        $this->createChild($module, [
            'description' => 'Clientes',
            'route_name' => 'ventas.clientes.index',
        ]);

        $this->createChild($module, [
            'description' => 'Notas Crédito',
            'route_name' => 'ventas.notas_credito.index',
        ]);

        $this->createChild($module, [
            'description' => 'Métodos Pago',
            'route_name' => 'ventas.metodos_pago.index',
        ]);

        $this->createChild($module, [
            'description' => 'Condiciones Pago',
            'route_name' => 'ventas.condiciones_pago.index'
        ]);
    }

    private function seedAbastecimiento(): void
    {
        $module = $this->createModule('Abastecimiento', 3, '');

        foreach (
            [
                ['Mesas', 'abastecimiento.mesas.index'],
                ['Tipo Plato', 'abastecimiento.tipos_plato.index'],
                ['Platos', 'abastecimiento.platos.index'],
                ['Programación', 'abastecimiento.programacion.index'],
            ] as [$desc, $route]
        ) {
            $this->createChild($module, [
                'description' => $desc,
                'route_name' => $route,
            ]);
        }
    }

    private function seedCuentas(): void
    {
        $module = $this->createModule('Cuentas', 4, 'credit-card-svgrepo-com.svg');

        /*$this->createChild($module, [
            'description' => 'Cuentas Cliente',
            'route_name' => 'cuentas.cliente.index',
        ]);*/

        $this->createChild($module, [
            'description' => 'Cuentas Proveedor',
            'route_name' => 'cuentas.proveedor.index',
        ]);
    }

    private function seedInventario(): void
    {
        $module = $this->createModule('Inventario', 5, 'warehouse-svgrepo-com.svg');

        foreach (
            [
                ['Categorías', 'inventario.categorias.index'],
                ['Marcas', 'inventario.marcas.index'],
                ['Productos', 'inventario.productos.index'],
                ['Kardex', 'inventario.kardex.index'],
                ['Inventario', 'inventario.inventario.index'],
                ['Kardex Valorizado', 'inventario.kardex_valorizado.index'],
                ['Nota Ingreso', 'inventario.nota_ingreso.index'],
                ['Nota Salida', 'inventario.nota_salida.index'],
            ] as [$desc, $route]
        ) {
            $this->createChild($module, [
                'description' => $desc,
                'route_name' => $route,
            ]);
        }
    }

    private function seedMostradorMesero(): void
    {
        $module = $this->createModule('Mostrador Mesero', 6, '');

        $this->createChild($module, [
            'description' => 'Mostrador',
            'route_name' => 'mostrador_mesero.mostrador.index',
        ]);
    }

    private function seedMostradorCajero(): void
    {
        $module = $this->createModule('Mostrador Cajero', 7, '');

        $this->createChild($module, [
            'description' => 'Mostrador',
            'route_name' => 'mostrador_cajero.mostrador.index',
        ]);
    }

    private function seedCompras(): void
    {
        $module = $this->createModule('Compras', 8, 'shopping-cart-svgrepo-com.svg');

        $this->createChild($module, [
            'description' => 'Proveedores',
            'route_name' => 'compras.proveedores.index',
        ]);

        $this->createChild($module, [
            'description' => 'Documento de Compra',
            'route_name' => 'compras.documento_compra.index',
        ]);
    }

    private function seedReportes(): void
    {
        $module = $this->createModule('Reportes', 9, 'analytics-financial-svgrepo-com.svg');

        $this->createChild($module, [
            'description' => 'Ventas Productos',
            'route_name' => 'reportes.ventas_productos.index'
        ]);

        $this->createChild($module, [
            'description' => 'Ventas Platos',
            'route_name' => 'reportes.ventas_platos.index'
        ]);

        $this->createChild($module, [
            'description' => 'Reporte de Venta',
            'route_name' => 'reportes.ventas.index',
        ]);

        $this->createChild($module, [
            'description' => 'Reporte Contable',
            'route_name' => 'reportes.contable.index',
        ]);
    }

    private function seedMantenimiento(): void
    {
        $module = $this->createModule('Mantenimiento', 10, 'configuration-svgrepo-com.svg', 1, 'landlord');

        $this->createChild($module, [
            'description' => 'Empresa',
            'route_name' => 'mantenimiento.empresas.index',
            'show' => 'landlord',
        ]);

        foreach (
            [
                ['Cuentas', 'mantenimiento.cuentas.index'],
                ['Cargos', 'mantenimiento.cargos.index'],
                ['Colaboradores', 'mantenimiento.colaboradores.index'],
                ['Usuarios', 'mantenimiento.usuario.index'],
                ['Roles', 'mantenimiento.roles.index'],
            ] as [$desc, $route]
        ) {
            $this->createChild($module, [
                'description' => $desc,
                'route_name' => $route,
                'show' => 'tenant',
            ]);
        }

        $this->createChild($module, [
            'description' => 'Configuración',
            'route_name' => 'mantenimiento.configuracion.index',
        ]);
    }

    private function seedConsultas(): void
    {
        $module = $this->createModule('Consultas', 11, 'analytics-report-svgrepo-com.svg');

        $this->createChild($module, [
            'description' => 'Créditos',
            'route_name' => 'consultas.creditos.index',
        ]);
    }
}
