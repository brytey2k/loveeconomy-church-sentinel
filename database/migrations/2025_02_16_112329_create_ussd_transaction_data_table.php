<?php

declare(strict_types=1);

use App\Enums\UssdTransactionState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs.ussd_transaction_data', static function (Blueprint $table) {
            $table->id();
            $table->string('ussd_sessions_id');
            $table->json('tx_data');
            $table->tinyInteger('status')->default(UssdTransactionState::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs.ussd_transaction_data');
    }
};
