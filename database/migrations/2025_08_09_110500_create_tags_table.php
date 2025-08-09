<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('church.tags', static function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // stable machine key, e.g., 'partner'
            $table->string('name'); // display name
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church.tags');
    }
};
