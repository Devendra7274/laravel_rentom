@push('scripts')

@endpush

<x-app-layout :assets="$assets ?? []">
<div>
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div class="header-title">
                  <h4 class="card-title">Add Room</h4>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                      <!-- Add Room Form -->
                    <form method="POST" action="{{ route('rooms.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="email">Room Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Room Name (Room No - 1)" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Room Rent</label>
                            <input type="number" class="form-control" name="number" placeholder="Room Rent" required>
                        </div>
                      
                        <button type="submit" class="btn btn-primary">Add Room</button>
                    </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div class="header-title">
                  <h4 class="card-title">All Room</h4>
               </div>
            </div>
            <div class="card-body">
               <div class="table-responsive">
                      <!-- Add Room Form -->
                       <table class="table table-striped">
                        <thead>
                           <th>#</th>
                           <th>Room Name</th>
                           <th>Room Rent(₹)</th>
                           <th>Action</th>
                        </thead>
                        <tbody>
                           {{ $sr = 1 }}
                        @foreach($rooms as $room)
                              <tr>
                                 <td>{{ $sr }}</td>
                                 <td>{{ $room->name }}</td>
                                 <td>{{ $room->number }}</td>
                                 <td>
                                    <a href=""><svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                    <path d="M11.4925 2.78906H7.75349C4.67849 2.78906 2.75049 4.96606 2.75049 8.04806V16.3621C2.75049 19.4441 4.66949 21.6211 7.75349 21.6211H16.5775C19.6625 21.6211 21.5815 19.4441 21.5815 16.3621V12.3341" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.82812 10.921L16.3011 3.44799C17.2321 2.51799 18.7411 2.51799 19.6721 3.44799L20.8891 4.66499C21.8201 5.59599 21.8201 7.10599 20.8891 8.03599L13.3801 15.545C12.9731 15.952 12.4211 16.181 11.8451 16.181H8.09912L8.19312 12.401C8.20712 11.845 8.43412 11.315 8.82812 10.921Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path d="M15.1655 4.60254L19.7315 9.16854" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></a>
                                    <a href="">                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                    <path d="M14.3955 9.59497L9.60352 14.387" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path d="M14.3971 14.3898L9.60107 9.59277" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.3345 2.75024H7.66549C4.64449 2.75024 2.75049 4.88924 2.75049 7.91624V16.0842C2.75049 19.1112 4.63549 21.2502 7.66549 21.2502H16.3335C19.3645 21.2502 21.2505 19.1112 21.2505 16.0842V7.91624C21.2505 4.88924 19.3645 2.75024 16.3345 2.75024Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                </svg>                            </a>
                                 </td>
                              </tr>
                              {{ $sr++ }}
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
