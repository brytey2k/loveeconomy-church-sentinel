<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.member_giving_types', static function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('giving_type_id');
            $table->timestamps();

            $table->primary(['member_id', 'giving_type_id']);
            $table->foreign('member_id')->references('id')->on('church.members')->cascadeOnDelete();
            $table->foreign('giving_type_id')->references('id')->on('church.giving_types')->cascadeOnDelete();

            $table->index(['member_id']);
            $table->index(['giving_type_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.member_giving_types');
    }
};
