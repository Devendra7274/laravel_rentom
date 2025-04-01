<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('room_rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('room_id', 10, 2);
            $table->decimal('electricity_bill', 10, 2);
            $table->enum('status', ['Paid', 'Pending'])->default('Pending');
            $table->date('month'); // Store as YYYY-MM-01
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('room_rents');
    }
};
