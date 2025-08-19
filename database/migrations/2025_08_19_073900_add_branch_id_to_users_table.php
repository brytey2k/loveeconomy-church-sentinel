<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('auth.users', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->constrained(table: 'organization.branches');
            $table->foreignId('stationed_branch_id')
                ->nullable()
                ->constrained(table: 'organization.branches');
        });
    }

    public function down(): void
    {
        Schema::table('auth.users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('stationed_branch_id');
        });
    }
};
