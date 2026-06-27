<?php

namespace App\Http\Services\Tenant\WCounter\Counter;

use App\Models\Tenant\Configuration;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Reservation\Reservation;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\Table\TableFusion;
use Exception;

class TableFusionService
{
    public function merge(array $data): void
    {
        $order_id        = (int) $data['order_id'];
        $master_table_id = (int) $data['master_table_id'];
        $slave_table_ids = array_map('intval', $data['slave_table_ids'] ?? []);

        if (empty($slave_table_ids)) {
            throw new Exception('Debe seleccionar al menos una mesa a unir.');
        }

        $order = Order::find($order_id);
        if (!$order || $order->status === 'ANULADO') {
            throw new Exception('El pedido no existe o está anulado.');
        }

        $masterReservation = Reservation::where('order_id', $order_id)
            ->where('table_id', $master_table_id)
            ->where('status', 'OCUPADO')
            ->first();

        if (!$masterReservation) {
            throw new Exception('La mesa principal no tiene un pedido activo asignado.');
        }

        $max = (int) (Configuration::find(5)->property ?? 4);
        $existingFusions = TableFusion::where('order_id', $order_id)
            ->where('status', 'ACTIVO')
            ->count();

        $totalAfterMerge = 1 + $existingFusions + count($slave_table_ids);
        if ($totalAfterMerge > $max) {
            throw new Exception("Máximo permitido: {$max} mesas por pedido. Ya tiene " . (1 + $existingFusions) . " y quiere agregar " . count($slave_table_ids) . ".");
        }

        foreach ($slave_table_ids as $slave_id) {
            if ($slave_id === $master_table_id) {
                throw new Exception("La mesa esclava no puede ser la misma que la mesa principal.");
            }

            $alreadyFused = TableFusion::where('slave_table_id', $slave_id)
                ->where('status', 'ACTIVO')
                ->exists();

            if ($alreadyFused) {
                throw new Exception("La mesa ID {$slave_id} ya está fusionada en otro pedido.");
            }

            $slaveTable = Table::find($slave_id);
            if (!$slaveTable || $slaveTable->status === 'ANULADO') {
                throw new Exception("La mesa ID {$slave_id} no existe o está anulada.");
            }

            $hasActiveReservation = Reservation::where('table_id', $slave_id)
                ->where('status', 'OCUPADO')
                ->exists();

            if ($hasActiveReservation) {
                throw new Exception("La mesa ID {$slave_id} tiene un pedido activo.");
            }

            Reservation::create([
                'table_id'    => $slave_id,
                'order_id'    => $order_id,
                'customer_id' => $order->customer_id ?? 1,
                'date'        => now(),
                'status'      => 'OCUPADO',
            ]);

            TableFusion::create([
                'order_id'        => $order_id,
                'master_table_id' => $master_table_id,
                'slave_table_id'  => $slave_id,
                'status'          => 'ACTIVO',
            ]);

            $slaveTable->status = 'OCUPADO';
            $slaveTable->save();
        }
    }

    public function unmerge(int $fusion_id): void
    {
        $fusion = TableFusion::find($fusion_id);

        if (!$fusion) {
            throw new Exception('Fusión no encontrada.');
        }

        if ($fusion->status !== 'ACTIVO') {
            throw new Exception('La fusión ya no está activa.');
        }

        Reservation::where('table_id', $fusion->slave_table_id)
            ->where('order_id', $fusion->order_id)
            ->where('status', 'OCUPADO')
            ->update(['status' => 'ANULADO']);

        $slaveTable = Table::find($fusion->slave_table_id);
        if ($slaveTable) {
            $slaveTable->status = 'LIBRE';
            $slaveTable->save();
        }

        $fusion->status = 'ANULADO';
        $fusion->save();
    }

    public function updateMasterTable(int $order_id, int $new_master_table_id): void
    {
        TableFusion::where('order_id', $order_id)
            ->where('status', 'ACTIVO')
            ->update(['master_table_id' => $new_master_table_id]);
    }

    public function closeFusionsByOrder(int $order_id, string $final_status = 'FINALIZADO'): void
    {
        $reservationStatus = $final_status === 'ANULADO' ? 'ANULADO' : 'FINALIZADO';

        $fusions = TableFusion::where('order_id', $order_id)
            ->where('status', 'ACTIVO')
            ->get();

        foreach ($fusions as $fusion) {
            Reservation::where('table_id', $fusion->slave_table_id)
                ->where('order_id', $order_id)
                ->where('status', 'OCUPADO')
                ->update(['status' => $reservationStatus]);

            $slaveTable = Table::find($fusion->slave_table_id);
            if ($slaveTable) {
                $slaveTable->status = 'LIBRE';
                $slaveTable->save();
            }

            $fusion->status = $final_status;
            $fusion->save();
        }
    }
}
