@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .tenant-card {
        border-left: 4px solid #0d6efd;
    }
    .payment-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .month-card {
        position: relative;
    }
    .stats-card {
        border-radius: 10px;
        overflow: hidden;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function(){
    // Room selection change
    $('#room_id').change(function(){
        let roomId = $(this).val();
        if (roomId) {
            $('#loading-spinner').removeClass('d-none');
            $.ajax({
                url: "{{ route('room_rent.details') }}",
                type: "GET",
                data: { room_id: roomId },
                success: function(response) {
                    $('#room_details').html(response.html);
                    $('#loading-spinner').addClass('d-none');
                    
                    // Initialize any tooltips or popovers
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    // Show room details section
                    $('#room-details-section').removeClass('d-none');
                },
                error: function(xhr) {
                    console.error("Error loading room details:", xhr.responseText);
                    $('#loading-spinner').addClass('d-none');
                    $('#room_details').html('<div class="alert alert-danger">Error loading room details. Please try again.</div>');
                }
            });
        } else {
            $('#room_details').html('');
            $('#room-details-section').addClass('d-none');
        }
    });
    
    // Filter payment history
    $('#filter_status').change(function(){
        const status = $(this).val();
        if (status === 'all') {
            $('.payment-row').show();
        } else {
            $('.payment-row').hide();
            $(`.payment-row[data-status="${status}"]`).show();
        }
    });
    
    // Search functionality
    $('#search_tenant').on('input', function(){
        const searchTerm = $(this).val().toLowerCase();
        $('.tenant-row').each(function(){
            const tenantName = $(this).find('.tenant-name').text().toLowerCase();
            const tenantRoom = $(this).find('.tenant-room').text().toLowerCase();
            
            if (tenantName.includes(searchTerm) || tenantRoom.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Date picker initialization
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
});
</script>
@endpush

<x-app-layout>
<div>
   <div class="row">
      <div class="col-md-12 mb-4">
         <div class="card border-0 shadow-sm rounded-lg">
            <div class="card-header bg-white py-3 border-bottom">
               <div class="d-flex justify-content-between align-items-center">
                  <h4 class="card-title mb-0">
                     <i class="fas fa-home text-primary me-2"></i> Manage Room Rent & Utilities
                  </h4>
                  <div class="dropdown">
                     <button class="btn btn-outline-primary dropdown-toggle" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-1"></i> Actions
                     </button>
                     <ul class="dropdown-menu" aria-labelledby="actionDropdown">
                        <li><a class="dropdown-item" href="#" id="printReports"><i class="fas fa-print me-2"></i> Print Reports</a></li>
                        <li><a class="dropdown-item" href="#" id="exportExcel"><i class="fas fa-file-excel me-2"></i> Export to Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" id="sendReminders"><i class="fas fa-bell me-2"></i> Send Reminders</a></li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <!-- Room Selection -->
               <div class="row">
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label text-muted">Select Room</label>
                        <div class="input-group">
                           <span class="input-group-text bg-light"><i class="fas fa-door-open"></i></span>
                           <select id="room_id" name="room_id" class="form-select">
                              <option value="">-- Select Room --</option>
                              @foreach($rooms as $room)
                                 <option value="{{ $room->id }}">Room {{ $room->name }} - {{ $room->users->count() }} Tenant(s)</option>
                              @endforeach
                           </select>
                  </div>
               </div>
               <br>

               <!-- Room & User Details -->
               <div id="room_details"></div>
            </div>
         </div>
      </div>

      <!-- Rent Calculation -->
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h4 class="card-title">Month-wise Calculation</h4>
            </div>
            <div class="card-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th>Month</th>
                        <th>Rent</th>
                        <th>Electricity Bill</th>
                        <th>Previous Due</th>
                        <th>Total Due</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($rents as $rent)
                     <tr>
                        <td>{{ \Carbon\Carbon::parse($rent->month)->format('F Y') }}</td>
                        <td>₹{{ $rent->rent_amount }}</td>
                        <td>₹{{ $rent->electricity_bill }}</td>
                        <td>₹{{ $rent->previous_due }}</td>
                        <td>₹{{ $rent->total_due }}</td>
                        <td>{{ $rent->status }}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

</x-app-layout>
