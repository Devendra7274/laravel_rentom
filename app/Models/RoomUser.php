<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model {
    use HasFactory;
    protected $fillable = ['room_id', 'name', 'email', 'phone', 'address', 'aadhar', 'pan', 'profile_pic', 'unit'];

    public function room() {
        return $this->belongsTo(Room::class);
    }
}
