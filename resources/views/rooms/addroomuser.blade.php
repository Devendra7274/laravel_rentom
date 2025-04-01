@push('scripts')
@endpush

<x-app-layout :assets="$assets ?? []">
<div>
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div class="header-title">
                  <h4 class="card-title">Add Room User</h4>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <!-- Add User Form -->
                  <form method="POST" action="{{ route('room_users.store') }}" enctype="multipart/form-data">
                     @csrf
                     
                     <!-- Room Dropdown -->
                     <div class="form-group">
                         <label class="form-label" for="room_id">Select Room</label>
                         <select name="room_id" class="form-control" required>
                             <option value="">-- Select Room --</option>
                             @foreach($rooms as $room)
                                 <option value="{{ $room->id }}">{{ $room->name }}</option>
                             @endforeach
                         </select>
                     </div>

                     <div class="form-group">
                         <label class="form-label" for="name">Name</label>
                         <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="email">Email</label>
                         <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="phone">Phone</label>
                         <input type="text" name="phone" class="form-control" placeholder="Enter Phone" required>
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="address">Address</label>
                         <textarea name="address" class="form-control" placeholder="Enter Address" required></textarea>
                     </div>
                     <div class="form-group">
                         <label class="form-label">Upload Aadhar</label>
                         <input type="file" class="form-control" name="aadhar">
                     </div>
                     <div class="form-group">
                         <label class="form-label">Upload PAN Card</label>
                         <input type="file" class="form-control" name="pan">
                     </div>
                     <div class="form-group">
                         <label class="form-label">Upload Profile Picture</label>
                         <input type="file" class="form-control" name="profile_pic">
                     </div>
                     <div class="form-group">
                         <label class="form-label">From Electricity Unit</label>
                         <input type="number" class="form-control" name="unit">
                     </div>
                     <button type="submit" class="btn btn-primary">Add User</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</x-app-layout>
