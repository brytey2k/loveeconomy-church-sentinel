<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization.branches', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('level_id')->constrained('organization.levels')->noActionOnDelete();
            $table->foreignId('country_id')->constrained('organization.countries')->noActionOnDelete();
            $table->string('currency', 3)->default('GHS');
            $table->bigInteger('parent_id')->nullable();
            $table->labelTree('path');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization.branches');
    }
};
