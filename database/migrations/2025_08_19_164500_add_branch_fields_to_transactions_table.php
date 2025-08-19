<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('finance.transactions', static function (Blueprint $table) {
            // Snapshot of the branch context at booking time
            $table->foreignId('branch_id')
                ->after('member_id')
                ->constrained(table: 'organization.branches');

            // Branch-preferred reporting currency at booking time (ISO 4217)
            $table->char('branch_reporting_currency', 3)
                ->after('reporting_currency');

            // Amount converted to branch currency in minor units
            $table->bigInteger('branch_converted_raw')
                ->after('converted_raw');

            // Index to speed up branch-level queries
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('finance.transactions', static function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn('branch_reporting_currency');
            $table->dropColumn('branch_converted_raw');
        });
    }
};
