<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\ModuleChild;
use App\Models\ModuleGrandChild;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCaja();
        $this->seedVentas();
        $this->seedAbastecimiento();
        $this->seedCuentas();
        $this->seedInventario();
        $this->seedMostradorMesero();
        $this->seedCompras();
        $this->seedReportes();
        $this->seedMantenimiento();
        $this->seedConsultas();
    }

    /* =========================
        Helpers
    ========================== */

    private function createModule(string $description, int $order = 1, ?string $show = null): Module
    {
        return Module::create(compact('description', 'order', 'show'));
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

    /* =========================
        Seeds por módulo
    ========================== */

    private function seedCaja(): void
    {
        $module = $this->createModule('Cajas');

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
        $module = $this->createModule('Ventas');

        $this->createChild($module, [
            'description' => 'Comprobante Venta',
            'route_name' => 'ventas.comprobante_venta.index',
        ]);

        $this->createChild($module, [
            'description' => 'Clientes',
            'route_name' => 'ventas.clientes.index',
        ]);

        $this->createChild($module, [
            'description' => 'Métodos Pago',
            'route_name' => 'ventas.metodos_pago.index',
        ]);
    }

    private function seedAbastecimiento(): void
    {
        $module = $this->createModule('Abastecimiento');

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
        $module = $this->createModule('Cuentas');

        $this->createChild($module, [
            'description' => 'Cuentas Cliente',
            'route_name' => 'cuentas.cliente.index',
        ]);

        $this->createChild($module, [
            'description' => 'Cuentas Proveedor',
            'route_name' => 'cuentas.proveedor.index',
        ]);
    }

    private function seedInventario(): void
    {
        $module = $this->createModule('Inventario');

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
        $module = $this->createModule('Mostrador Mesero');

        $this->createChild($module, [
            'description' => 'Mostrador',
            'route_name' => 'mostrador_mesero.mostrador.index',
        ]);
    }

    private function seedCompras(): void
    {
        $module = $this->createModule('Compras');

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
        $module = $this->createModule('Reportes');

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
        $module = $this->createModule('Mantenimiento', 1, 'landlord');

        $this->createChild($module, [
            'description' => 'Empresa',
            'route_name' => 'mantenimiento.empresa.index',
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
        $module = $this->createModule('Consultas');

        $this->createChild($module, [
            'description' => 'Créditos',
            'route_name' => 'consultas.creditos.index',
        ]);
    }
}
