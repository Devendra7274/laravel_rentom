<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomRent extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'room_id', 'rent_amount', 'electricity_bill', 'previous_due', 'total_due', 'month', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function calculateTotalDue($user_id, $month) {
        $previousRent = self::where('user_id', $user_id)
            ->where('month', '<', $month)
            ->latest('month')
            ->first();

        $previous_due = $previousRent && $previousRent->status == 'Pending' ? $previousRent->total_due : 0;

        return $previous_due;
    }
}
