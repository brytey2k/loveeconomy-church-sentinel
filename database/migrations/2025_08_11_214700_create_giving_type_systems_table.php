<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.giving_type_systems', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('giving_type_id')->constrained('church.giving_types')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('church.giving_type_systems')->nullOnDelete();
            $table->string('name');
            $table->decimal('amount_low', 12, 2)->default(0);
            $table->decimal('amount_high', 12, 2)->default(0);
            $table->boolean('assignable')->default(true);
            $table->boolean('auto_assignable')->default(false);
            $table->labelTree('path');
            $table->timestamps();

            $table->unique(['giving_type_id', 'path']);
            $table->index(['giving_type_id']);
            $table->index(['parent_id']);
            $table->index(['assignable']);
            $table->index(['auto_assignable']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.giving_type_systems');
    }
};
