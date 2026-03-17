<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            DROP FUNCTION IF EXISTS fn_stock_consumable;
            CREATE FUNCTION fn_stock_consumable(p_almacen_id INT, p_consumable_id INT, p_fecha DATE)
            RETURNS DECIMAL(16,6)
            DETERMINISTIC
            BEGIN
                DECLARE p_stock DECIMAL(16,6) DEFAULT 0;

                SELECT
                    COALESCE(SUM(cantidad_entrada) - SUM(cantidad_salida), 0)
                INTO p_stock
                FROM (

                    SELECT
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_entrada,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS cantidad_salida
                    FROM consumable_kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.consumable_id = p_consumable_id
                    AND DATE(k.date) < p_fecha

                ) AS t;

                RETURN p_stock;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fn_stock_consumable');
    }
};
