<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('room_rents', function (Blueprint $table) {
            $table->string('month', 7)->change(); // Change month from DATE to VARCHAR(7)
            $table->decimal('previous_due', 10, 2)->default(0)->after('electricity_bill');
            $table->decimal('total_due', 10, 2)->default(0)->after('previous_due');
            $table->string('previous_unit')->default(0)->change();
        });
    }

    public function down() {
        Schema::table('room_rents', function (Blueprint $table) {
            $table->date('month')->change(); // Revert back to DATE if rolling back
            $table->dropColumn(['previous_due', 'total_due']);
        });
    }
};
