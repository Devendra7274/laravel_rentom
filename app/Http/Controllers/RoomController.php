<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomUser;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller {
    public function index() {
        $rooms = Room::with('users')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function viewUser(){
        $rooms = Room::with('users')->get();
        return view('rooms/roomuser', compact('rooms'));
    }
    public function addRoomUser(){
        $rooms = Room::with('users')->get();
        return view('rooms/addroomuser', compact('rooms'));
    }
   
   
    
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|integer',
        ]);
        Room::create($request->all());
        return back()->with('success', 'Room added successfully!');
    }

    public function update(Request $request, Room $room) {
        $request->validate([
            'name' => 'required|string',
            'number' => 'required|integer',
        ]);
        $room->update($request->all());
        return back()->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room) {
        $room->delete();
        return back()->with('success', 'Room deleted successfully!');
    }

    public function storeUser(Request $request) {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string',
            'email' => 'required|email|unique:room_users,email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'unit' => 'required|numeric', // Ensure 'unit' is provided
            'aadhar' => 'nullable|file|mimes:pdf,jpg,png',
            'pan' => 'nullable|file|mimes:pdf,jpg,png',
            'profile_pic' => 'nullable|image|mimes:jpg,png',
        ]);
    
        $data = $request->all(); // Ensure all validated data is retrieved
    
        // Check and store files
        if ($request->hasFile('aadhar')) {
            $data['aadhar'] = $request->file('aadhar')->store('uploads', 'public');
        }
        if ($request->hasFile('pan')) {
            $data['pan'] = $request->file('pan')->store('uploads', 'public');
        }
        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('uploads', 'public');
        }
    
        // Ensure 'unit' exists in the insert query
        RoomUser::create([
            'room_id' => $data['room_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'unit' => $data['unit'],  // 🔥 Ensure 'unit' is included
            'aadhar' => $data['aadhar'] ?? null,
            'pan' => $data['pan'] ?? null,
            'profile_pic' => $data['profile_pic'] ?? null,
        ]);
    
        return back()->with('success', 'User added successfully!');
    }
    

    public function destroyUser(RoomUser $roomUser) {
        if ($roomUser->profile_pic) Storage::delete('public/' . $roomUser->profile_pic);
        if ($roomUser->aadhar) Storage::delete('public/' . $roomUser->aadhar);
        if ($roomUser->pan) Storage::delete('public/' . $roomUser->pan);
        $roomUser->delete();
        return back()->with('success', 'User deleted successfully!');
    }
}
