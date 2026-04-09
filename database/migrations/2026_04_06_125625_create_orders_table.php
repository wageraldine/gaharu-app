<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('items');          // snapshot produk [{name,price,qty,subtotal}]
            $table->unsignedBigInteger('total');
            $table->string('status')->default('pending_payment');
            // status: pending_payment | waiting_confirmation | paid | cancelled
            $table->string('payment_proof')->nullable(); // path file bukti bayar
            $table->text('notes')->nullable();           // catatan user
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
