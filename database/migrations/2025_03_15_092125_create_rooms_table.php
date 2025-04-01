<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number');
            $table->timestamps();
        });

        Schema::create('room_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('unit');
            $table->string('aadhar')->nullable();
            $table->string('pan')->nullable();
            $table->string('profile_pic')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('room_users');
        Schema::dropIfExists('rooms');
    }
};
