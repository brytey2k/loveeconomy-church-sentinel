<?php

declare(strict_types=1);

use App\Enums\TransactionState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance.transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id');
            $table->char('type');
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('status')->default(TransactionState::SUCCESSFUL);
            $table->integer('month_paid_for');
            $table->integer('year_paid_for');
            $table->string('paid_through')->nullable()->comment('The payment provider/processor used');
            $table->string('order_id')->nullable()->comment('The order ID from the payment provider');
            $table->string('payment_type')->nullable()->comment('whether momo, card, etc');
            $table->string('session_id')->nullable()->comment('session id if it was a ussd transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance.transactions');
    }
};
