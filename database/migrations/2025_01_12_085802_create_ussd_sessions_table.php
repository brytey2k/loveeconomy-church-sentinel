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
        Schema::create('logs.ussd_sessions', static function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('phone_number');
            $table->string('service_code');
            $table->string('operator');
            $table->longText('log_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs.ussd_sessions');
    }
};
