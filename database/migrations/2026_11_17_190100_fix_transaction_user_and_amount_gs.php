<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('transactions')->whereNull('amount_gs')->update(['amount_gs' => DB::raw('amount')]);

        Schema::table('transactions', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->dropForeign(['user_id']);
            }

            $table->unsignedBigInteger('user_id')->nullable()->change();

            if (DB::getDriverName() === 'mysql') {
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                $table->dropForeign(['user_id']);
            }

            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            if (DB::getDriverName() === 'mysql') {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }
};
