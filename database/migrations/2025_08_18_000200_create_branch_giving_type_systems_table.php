<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.branch_giving_type_systems', static function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('giving_type_system_id');
            $table->timestampsTz();

            $table->primary(['branch_id', 'giving_type_system_id']);

            $table->foreign('branch_id')->references('id')->on('organization.branches')->cascadeOnDelete();
            $table->foreign('giving_type_system_id')->references('id')->on('church.giving_type_systems')->cascadeOnDelete();

            $table->index(['branch_id']);
            $table->index(['giving_type_system_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.branch_giving_type_systems');
    }
};
