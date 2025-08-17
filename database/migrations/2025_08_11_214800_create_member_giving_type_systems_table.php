<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.member_giving_type_systems', static function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('giving_type_system_id');
            $table->timestamps();

            $table->primary(['member_id', 'giving_type_system_id']);

            $table->foreign('member_id')
                ->references('id')->on('church.members')->cascadeOnDelete();

            $table->foreign('giving_type_system_id')
                ->references('id')->on('church.giving_type_systems')->cascadeOnDelete();

            $table->index(['member_id']);
            $table->index(['giving_type_system_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.member_giving_type_systems');
    }
};
