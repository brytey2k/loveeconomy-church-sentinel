<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance.fx_rates_history', static function (Blueprint $table) {
            $table->id();

            // We store currencies as ISO 4217 char(3). In code, prefer App\Enums\Currency for safety.
            $table->char('base_currency', 3)
                ->comment('Base currency ISO 4217 code (e.g., USD). See App\\Enums\\Currency for allowed values.');
            $table->char('quote_currency', 3)
                ->comment('Quote currency ISO 4217 code (e.g., GHS). See App\\Enums\\Currency for allowed values.');

            $table->decimal('rate', 20, 10)
                ->comment('FX rate as base->quote (1 base_currency equals `rate` quote_currency). High precision decimal.');

            $table->dateTimeTz('as_of_hour')
                ->comment('Timestamp of the hourly snapshot (UTC recommended). Should be truncated to the start of the hour.');

            $table->string('source')->nullable()
                ->comment('Source of the rate snapshot (e.g., ECB, OXR, internal).');
            $table->jsonb('meta')->nullable()
                ->comment('Optional provider payload or annotations for audit.');

            $table->timestampsTz();

            // Ensure no duplicate snapshot for the same pair and hour
            $table->unique(['base_currency', 'quote_currency', 'as_of_hour'], 'fx_rates_history_pair_hour_unique');

            // Common query filters
            $table->index(['as_of_hour'], 'fx_rates_history_as_of_idx');
            $table->index(['base_currency', 'quote_currency'], 'fx_rates_history_pair_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance.fx_rates_history');
    }
};
