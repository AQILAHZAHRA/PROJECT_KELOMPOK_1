<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->decimal('total_berat', 8, 2)->default(0)->after('jumlah');
            $table->json('items')->nullable()->after('total_berat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->dropColumn(['total_berat', 'items']);
        });
    }
};
