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
            DROP PROCEDURE IF EXISTS sp_kardex_consumable;
            CREATE PROCEDURE sp_kardex_consumable(
                IN p_almacen_id INT,
                IN p_consumable_id INT,
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
            BEGIN
                -- Variables de stock por producto
                DECLARE stock_actual INT DEFAULT 0;
                DECLARE stock_anterior INT DEFAULT 0;
                DECLARE producto_actual INT DEFAULT NULL;

                -- Reset de variables
                SET @rownum := 0;
                SET @stock_actual := 0, @stock_anterior := 0, @producto_actual := NULL;

                -- Consulta del historial de stock con filtros
                SELECT
                    @rownum := @rownum + 1 AS kardex_order,
                    t.consumable_id,
                    t.date,
                    t.type,
                    t.document_serie,
                    t.warehouse_name,
                    t.consumable_name,
                    t.category_name,
                    t.brand_name,
                    t.creator_user_name,

                    -- SI EL PRODUCTO ES EL MISMO COLOCAR EL STOCK POSTERIOR ANTERIOR, CASO CONTRARIO CALCULAR STOCK
                    @stock_anterior := IF(
                        @producto_actual = t.consumable_id,
                        @stock_actual,
                        fn_stock(p_almacen_id, t.consumable_id, p_fecha_inicio)
                    ) AS previous_stock,

                    -- Asignamos el valor de stock posterior
                    t.quantity_in AS entrada,
                    t.quantity_out AS salida,

                    -- Calculamos el stock posterior
                    @stock_actual := @stock_anterior + t.quantity_in - t.quantity_out AS later_stock,

                    -- Asignamos el producto actual para controlar la iteración
                    @producto_actual := t.consumable_id AS producto_actual
                FROM (

                    -- KARDEX
                    SELECT
                        k.consumable_id,
                        k.date,
                        k.type,
                        k.document_serie,
                        k.warehouse_name,
                        k.consumable_name,
                        k.category_name,
                        k.brand_name,
                        k.creator_user_name,
                        CASE
                            WHEN k.type = 'ENTRADA' THEN k.quantity
                            ELSE 0
                        END AS quantity_in,

                        CASE
                            WHEN k.type = 'SALIDA' THEN k.quantity
                            ELSE 0
                        END AS quantity_out
                    FROM consumable_kardex AS k
                    WHERE k.warehouse_id = p_almacen_id
                    AND k.consumable_id = p_consumable_id
                    AND (p_fecha_inicio IS NOT NULL
                    AND p_fecha_fin IS NOT NULL
                    AND DATE(k.date) BETWEEN p_fecha_inicio AND p_fecha_fin)

                ) t
                ORDER BY t.consumable_id, t.date ASC;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_kardex_consumable');
    }
};
