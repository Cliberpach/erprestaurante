<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exit_money', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proof_payment_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id');
            $table->string('number', 15);
            $table->date('date');

            $table->unsignedBigInteger('cost_center_id');
            $table->string('cost_center_name', 200);

            $table->double('total');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('proof_payment_id')->references('id')->on('proof_payments');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cost_center_id')->references('id')->on('cost_center');

            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('petty_cash_book_id');
            $table->string('payment_method_name', 160);

            $table->boolean('discount_cash')->default(false);

            $table->unsignedBigInteger('consumable_purchse_id')->nullable();
            $table->foreign('consumable_purchse_id')->references('id')->on('consumable_purchases');

            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('editor_user_id')->nullable();
            $table->unsignedBigInteger('deletor_user_id')->nullable();

            $table->string('deletor_user_name')->nullable();
            $table->string('editor_user_name')->nullable();
            $table->string('creator_user_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_money');
    }
};
