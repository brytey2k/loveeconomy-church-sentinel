<?php

declare(strict_types=1);

use App\Enums\TransactionState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('finance.church_transactions', static function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz('tx_date')->comment('the actual date and time of the transaction');
            $table->foreignId('branch_id');
            $table->foreignId('giving_type_id');
            $table->foreignId('giving_type_system_id')->nullable();
            $table->tinyInteger('status')->default(TransactionState::SUCCESSFUL);
            $table->integer('month_paid_for');
            $table->integer('year_paid_for');

            // Payment provider details
            $table->string('paid_through')->nullable()->comment('The payment provider/processor used');
            $table->string('order_id')->nullable()->comment('The order ID from the payment provider');
            $table->string('payment_type')->nullable()->comment('whether momo, card, etc');
            $table->string('session_id')->nullable()->comment('session id if it was a ussd transaction');

            // Core monetary fields
            $table->bigInteger('amount_raw')
                ->comment('Original amount stored in minor units (integer). Example: GHS 12.34 => 1234. Avoid floats.');
            $table->char('currency', 3)
                ->comment('ISO 4217 currency code of the original/booked amount (e.g., GHS, USD, EUR).');

            // FX conversion snapshot used at booking time
            $table->decimal('fx_rate', 20, 10)
                ->comment('Rate used to convert currency to reporting_currency at booking time. 1 when currency == reporting_currency.');
            $table->char('reporting_currency', 3)
                ->comment('ISO 4217 code used for consolidated reporting (e.g., GHS). Stored per row for explicitness/audit.');
            $table->bigInteger('converted_raw')
                ->comment('Amount converted to reporting_currency in minor units using fx_rate and deterministic rounding.');

            // Audit trail for original user input
            $table->string('original_amount_entered')->nullable()
                ->comment('The exact user-entered amount text preserved for audit (e.g., "100").');
            $table->char('original_amount_currency', 3)->nullable()
                ->comment('Currency as originally entered by the user (ISO 4217). Useful if parsing differs from stored currency.');

            $table->timestampsTz();

            $table->foreign('branch_id')->references('id')->on('organization.branches')->cascadeOnDelete();
            $table->foreign('giving_type_id')->references('id')->on('church.giving_types');
            $table->foreign('giving_type_system_id')->references('id')->on('church.giving_type_systems');

            $table->index(['branch_id', 'tx_date']);
            $table->index(['giving_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance.church_transactions');
    }
};
