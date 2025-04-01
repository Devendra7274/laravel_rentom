<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomRent;
use App\Models\User;
use Carbon\Carbon;

class RoomRentController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all users
        $rooms = Room::all(); // Fetch all rooms
        $rents = RoomRent::with('user')->get(); // Fetch all rent records with user details
    
        return view('room_rent.index', compact('users', 'rooms', 'rents'));
    }
    public function store(Request $request)
    {
        // Debug: Check if rent_amount exists
        if (!$request->has('rent_amount')) {
            return redirect()->back()->withErrors(['rent_amount' => 'Rent amount is required.']);
        }
    
        // Convert to array if single value
        $rents = is_array($request->rent_amount) ? $request->rent_amount : [$request->rent_amount];
    
        foreach ($request->month as $index => $month) {
            // Ensure month format
            $formattedMonth = date('Y-m-01', strtotime($month));
    
            // Validate rent amount
            if (!isset($rents[$index]) || empty($rents[$index])) {
                return redirect()->back()->withErrors(['rent_amount' => 'Rent amount is required.']);
            }
    
            RoomRent::updateOrCreate(
                ['room_id' => $request->room_id, 'month' => $formattedMonth],
                [
                    'user_id' => auth()->id(),
                    'rent_amount' => $rents[$index],
                    'electricity_bill' => ($request->electricity_unit[$index] - $request->prev_unit[$index]) * 9,
                    'previous_due' => ($rents[$index] + (($request->electricity_unit[$index] - $request->prev_unit[$index]) * 9)) - $request->amount_paid[$index] ?? 0,
                    'total_due' => $request->total_due[$index] + ($request->electricity_unit[$index] - $request->prev_unit[$index]) * 9,
                    'amount_paid' => $request->amount_paid[$index] ?? 0,
                    'status' => ($request->amount_paid[$index] ?? 0) >= ($request->total_due[$index] ?? 0) ? 'Paid' : 'Pending',
                    'previous_unit' => isset($request->prev_unit[$index]) ? (int) $request->prev_unit[$index] : 0, // Ensure it's set and converted to an integer
                ]
            );
            
        }
    
        return redirect()->back()->with('success', 'Rent details saved successfully.');
    }
    
    public function getRoomDetails(Request $request) {
        $room = Room::with('users')->findOrFail($request->room_id);
        $lastRent = RoomRent::where('room_id', $room->id)->latest('month')->first();
        $prevUnit = $lastRent ? $lastRent->electricity_unit : 0;
    
        $html = view('room_rent.room_details', compact('room', 'prevUnit'))->render();
        return response()->json(['html' => $html]);
    }
    
}
