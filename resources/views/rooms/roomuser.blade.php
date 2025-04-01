@push('scripts')
@endpush

<x-app-layout :assets="$assets ?? []">
<div>
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div class="header-title">
                  <h4 class="card-title">All Room/Shop Users</h4>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <th>Profile</th>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Phone</th>
                           <th>Address</th>
                           <th>Room Number</th>
                           <th>Actions</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($rooms as $room)
                           @foreach($room->users as $user)
                           <tr>
                              <!-- Profile Picture -->
                              <td>
                                 <img src="{{ $user->profile_pic ? asset('storage/'.$user->profile_pic) : asset('default-profile.png') }}" 
                                      alt="Profile Picture" class="rounded-circle" width="50" height="50">
                              </td>
                              
                              <!-- Clickable Name to Open Popup -->
                              <td>
                                 <a href="#" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">
                                    {{ $user->name }}
                                 </a>
                              </td>

                              <td>{{ $user->email }}</td>
                              <td>{{ $user->phone }}</td>
                              <td>{{ $user->address }}</td>
                              <td>{{ $room->name}}</td>
                              
                              <!-- Delete User -->
                              <td>
                                 <form method="POST" action="{{ route('room_users.destroy', $user->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                 </form>
                              </td>
                           </tr>

                           <!-- User Documents Modal -->
                           <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog">
                                 <div class="modal-content">
                                    <div class="modal-header">
                                       <h5 class="modal-title">Documents of {{ $user->name }}</h5>
                                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                       <p><strong>Email:</strong> {{ $user->email }}</p>
                                       <p><strong>Phone:</strong> {{ $user->phone }}</p>
                                       <p><strong>Address:</strong> {{ $user->address }}</p>
                                       
                                       <!-- Aadhar Card -->
                                       @if($user->aadhar)
                                          <p><strong>Aadhar Card:</strong></p>
                                          <img src="{{ asset('storage/'.$user->aadhar) }}" class="img-fluid">
                                       @else
                                          <p>No Aadhar Card Uploaded</p>
                                       @endif

                                       <!-- PAN Card -->
                                       @if($user->pan)
                                          <p><strong>PAN Card:</strong></p>
                                          <img src="{{ asset('storage/'.$user->pan) }}" class="img-fluid">
                                       @else
                                          <p>No PAN Card Uploaded</p>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                           </div>

                           @endforeach
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</x-app-layout>
