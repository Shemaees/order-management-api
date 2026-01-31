<?php

use App\Modules\Payment\Enums\PaymentStatusEnum;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index()->constrained()->cascadeOnDelete();
            $table->string('payment_id')->unique();
            $table->string('payment_method')->index();
            $table->enum('payment_status', PaymentStatusEnum::values())
                ->default(PaymentStatusEnum::PENDING)->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('transaction_details');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
