<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.member_tags', static function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['member_id', 'tag_id']);
            $table->foreign('member_id')->references('id')->on('church.members')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('church.tags')->cascadeOnDelete();

            $table->index(['member_id']);
            $table->index(['tag_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.member_tags');
    }
};
