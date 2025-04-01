<div class="container mt-4">
    <!-- Room User Details with improved design -->
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-lg">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title fw-bold mb-0">Room Details</h5>
            <span class="badge bg-primary rounded-pill">Room #{{ $room->name }}</span>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="tenants-info">
                    <h6 class="text-muted mb-3 border-bottom pb-2">Tenants Information</h6>
                    @foreach($room->users as $user)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $user->profile_pic ? asset('storage/'.$user->profile_pic) : asset('default-profile.png') }}" 
                                alt="Profile Picture" class="rounded-circle border shadow-sm" width="60" height="60">
                            <div class="ms-3">
                                <strong class="fs-5 d-block">{{ $user->name }}</strong>
                                <div class="d-flex mt-1">
                                    <small class="text-muted me-3"><i class="fas fa-envelope me-1"></i> {{ $user->email }}</small>
                                    <small class="text-muted"><i class="fas fa-phone me-1"></i> {{ $user->phone }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                <div class="room-summary bg-light p-3 rounded">
                    <h6 class="text-muted mb-3 border-bottom pb-2">Room Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Monthly Rent:</span>
                        <strong>₹{{ $room->number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Occupancy Since:</span>
                        <strong>{{ \Carbon\Carbon::parse($room->created_at)->format('d M, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month-wise Calculation -->
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-lg">
        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">Month-wise Rent Collection</h5>
        
        @php
            $startDate = \Carbon\Carbon::parse($room->created_at)->startOfMonth();
            $currentDate = \Carbon\Carbon::now()->startOfMonth();
            $prevUnit = 0; // Default previous electricity unit
            $currentMonth = \Carbon\Carbon::now()->format('Y-m');
        @endphp

        <!-- Rent Form -->
        <form method="POST" action="{{ route('room_rent.store') }}" id="rentForm">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            <div class="row">
                @while ($startDate->lessThanOrEqualTo($currentDate))
                    @php
                        $monthYear = $startDate->format('Y-m');
                        $prevRent = \App\Models\RoomRent::where('room_id', $room->id)
                                    ->whereRaw("DATE_FORMAT(month, '%Y-%m') = ?", [$monthYear])
                                    ->first();
                        
                        $prevUnit = $prevRent ? $prevRent->current_unit : $prevUnit;
                        $previousDue = $prevRent && !$prevRent->is_paid_full ? ($prevRent->total_due - $prevRent->amount_paid) : 0;
                        $roomRent = $room->number;
                        $amountPaid = $prevRent ? $prevRent->amount_paid : 0;
                        $isPaid = $prevRent && $prevRent->is_paid_full;
                        $isCurrentMonth = ($monthYear == $currentMonth);
                        
                        // Get electricity bill from previous month if exists
                        $electricityBill = $prevRent ? $prevRent->electricity_bill : 0;
                    @endphp

                    <div class="col-md-12">
                        <div class="card mb-4 shadow-sm border rounded-lg p-0 {{ $isCurrentMonth ? 'border-primary' : '' }}">
                            <div class="card-header bg-{{ $isPaid ? 'success' : ($isCurrentMonth ? 'primary' : 'light') }} text-{{ $isPaid || $isCurrentMonth ? 'white' : 'dark' }} py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">{{ $startDate->format('F Y') }}</h5>
                                    
                                    @if($isPaid)
                                        <span class="badge bg-white text-success rounded-pill"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                    @elseif($prevRent && $amountPaid > 0)
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="fas fa-exclamation-circle me-1"></i> Partially Paid</span>
                                    @elseif($isCurrentMonth)
                                        <span class="badge bg-white text-primary rounded-pill"><i class="fas fa-calendar-day me-1"></i> Current Month</span>
                                    @else
                                        <span class="badge bg-danger text-white rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Unpaid</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-body p-3">
                                <input type="hidden" name="month[]" value="{{ $monthYear }}">

                                <div class="row">
                                    <!-- Electricity Calculation -->
                                    <div class="col-md-6">
                                        <div class="electricity-section bg-light p-3 rounded mb-3">
                                            <h6 class="text-danger mb-3 border-bottom pb-2"><i class="fas fa-bolt me-1"></i> Electricity Calculation</h6>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-5 col-form-label fw-bold">Previous Unit:</label> 
                                                <div class="col-sm-7">
                                                    <span class="form-control-plaintext text-muted">{{ $prevUnit }}</span>
                                                    <input type="hidden" name="prev_unit[]" value="{{ $prevUnit }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-5 col-form-label fw-bold">Current Unit:</label>
                                                <div class="col-sm-7">
                                                    @if($isPaid || ($prevRent && $prevRent->current_unit > 0))
                                                        <span class="form-control-plaintext">{{ $prevRent ? $prevRent->current_unit : 0 }}</span>
                                                        <input type="hidden" name="electricity_unit[]" value="{{ $prevRent ? $prevRent->current_unit : 0 }}">
                                                    @else
                                                        <input type="number" name="electricity_unit[]" class="form-control electricity_unit" data-prev-unit="{{ $prevUnit }}" required>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-5 col-form-label fw-bold">Electricity Cost:</label> 
                                                <div class="col-sm-7">
                                                    <div class="d-flex align-items-center">
                                                        <small class="text-muted me-2">(Current - Previous) × 9 =</small>
                                                        @if($isPaid || ($prevRent && $prevRent->electricity_bill > 0))
                                                            <span class="text-danger fw-bold">₹{{ $electricityBill }}</span>
                                                            <input type="hidden" name="electricity_bill[]" value="{{ $electricityBill }}">
                                                        @else
                                                            <span class="text-danger fw-bold electricity_bill">₹0</span>
                                                            <input type="hidden" name="electricity_bill[]" value="0">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rent & Total Calculation -->
                                    <div class="col-md-6">
                                        <div class="rent-section bg-light p-3 rounded mb-3">
                                            <h6 class="text-success mb-3 border-bottom pb-2"><i class="fas fa-home me-1"></i> Rent Calculation</h6>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-5 col-form-label fw-bold">Monthly Rent:</label> 
                                                <div class="col-sm-7">
                                                    <span class="form-control-plaintext">₹{{ $roomRent }}</span>
                                                    <input type="hidden" name="rent_amount[]" value="{{ $roomRent }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3 row">
                                                <label class="col-sm-5 col-form-label fw-bold">Previous Due:</label> 
                                                <div class="col-sm-7">
                                                    <span class="form-control-plaintext {{ $previousDue > 0 ? 'text-danger' : 'text-muted' }}">
                                                        ₹{{ $previousDue }}
                                                    </span>
                                                    <input type="hidden" name="previous_due[]" value="{{ $previousDue }}">
                                                </div>
                                            </div>
                                            
                                            <div class="total-section mt-3 p-2 bg-white rounded border">
                                                <div class="mb-2 row">
                                                    <label class="col-sm-5 col-form-label fw-bold text-primary">Total Amount:</label> 
                                                    <div class="col-sm-7">
                                                        @if($isPaid || ($prevRent && $prevRent->total_due > 0))
                                                            <span class="form-control-plaintext text-primary fw-bold">₹{{ $prevRent->total_due }}</span>
                                                            <input type="hidden" name="total_due[]" value="{{ $prevRent->total_due }}">
                                                        @else
                                                            <span class="form-control-plaintext text-primary fw-bold">₹<span class="total_due">{{ $roomRent + $previousDue }}</span></span>
                                                            <input type="hidden" name="total_due[]" value="{{ $roomRent + $previousDue }}">
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-2 row">
                                                    <label class="col-sm-5 col-form-label fw-bold">Amount Paid:</label>
                                                    <div class="col-sm-7">
                                                        @if($isPaid || $amountPaid > 0)
                                                            <span class="form-control-plaintext text-success fw-bold amount_paid_display" data-amount-paid="{{ $amountPaid }}">₹{{ $amountPaid }}</span>
                                                            <input type="hidden" name="amount_paid[]" value="{{ $amountPaid }}">
                                                            
                                                            @php
                                                                $totalAmount = $prevRent ? $prevRent->total_due : ($roomRent + $previousDue + $electricityBill);
                                                                $remainingDue = $totalAmount - $amountPaid;
                                                            @endphp
                                                            
                                                            @if($remainingDue > 0)
                                                                <div class="mt-2">
                                                                    <span class="badge bg-danger">Remaining: ₹{{ number_format($remainingDue, 2) }}</span>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="input-group">
                                                                <span class="input-group-text">₹</span>
                                                                <input type="number" name="amount_paid[]" class="form-control amount_paid" required>
                                                            </div>
                                                            <div class="remaining-due-container mt-2"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(!$isPaid && $isCurrentMonth)
                                    <div class="payment-methods mt-2 p-3 bg-light rounded">
                                        <h6 class="mb-3"><i class="fas fa-money-bill-wave me-1"></i> Payment Methods</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method[]" id="cash{{ $loop->index }}" value="cash" checked>
                                                    <label class="form-check-label" for="cash{{ $loop->index }}">
                                                        <i class="fas fa-money-bill text-success me-1"></i> Cash
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method[]" id="upi{{ $loop->index }}" value="upi">
                                                    <label class="form-check-label" for="upi{{ $loop->index }}">
                                                        <i class="fas fa-mobile-alt text-primary me-1"></i> UPI
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method[]" id="bank{{ $loop->index }}" value="bank">
                                                    <label class="form-check-label" for="bank{{ $loop->index }}">
                                                        <i class="fas fa-university text-dark me-1"></i> Bank Transfer
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @php
                        $startDate->addMonth();
                    @endphp
                @endwhile
            </div>
            
            <div class="text-center mt-3">
                <button class="btn btn-primary px-5 py-2" type="submit">
                    <i class="fas fa-save me-2"></i> Save Payment Details
                </button>
            </div>
        </form>
    </div>
    
    <!-- Payment Summary -->
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-lg">
        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
            <i class="fas fa-chart-line me-2"></i> Payment Summary
        </h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body p-3 text-center">
                        <h3 class="mb-0">₹{{ $room->users->count() * $room->number }}</h3>
                        <small>Monthly Revenue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body p-3 text-center">
                        <h3 class="mb-0">{{ \App\Models\RoomRent::where('room_id', $room->id)->where('is_paid_full', true)->count() }}</h3>
                        <small>Months Paid</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body p-3 text-center">
                        <h3 class="mb-0">{{ \App\Models\RoomRent::where('room_id', $room->id)->where('is_paid_full', false)->where('amount_paid', '>', 0)->count() }}</h3>
                        <small>Partially Paid</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body p-3 text-center">
                        <h3 class="mb-0">₹{{ \App\Models\RoomRent::where('room_id', $room->id)->sum(\DB::raw('total_due - amount_paid')) }}</h3>
                        <small>Total Due</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function() {
    // Function to calculate electricity bill
    function calculateElectricityBill(currentUnit, prevUnit) {
        const unitsUsed = Math.max(0, currentUnit - prevUnit);
        return unitsUsed * 9; // 9 rupees per unit
    }

    // Function to update totals
    function updateTotals(row) {
        const $row = $(row).closest('.card');
        const prevUnit = parseFloat($row.find('.electricity_unit').data('prev-unit')) || 0;
        const currentUnit = parseFloat($row.find('.electricity_unit').val()) || 0;
        
        // Calculate electricity bill
        const electricityBill = calculateElectricityBill(currentUnit, prevUnit);
        
        // Update electricity bill display and hidden input
        $row.find('.electricity_bill').text('₹' + electricityBill);
        $row.find('input[name="electricity_bill[]"]').val(electricityBill);
        
        // Get rent amount and previous due
        const rentAmount = parseFloat($row.find('input[name="rent_amount[]"]').val()) || 0;
        const previousDue = parseFloat($row.find('input[name="previous_due[]"]').val()) || 0;
        
        // Calculate total due
        const totalDue = rentAmount + previousDue + electricityBill;
        
        // Update total due display and hidden input
        $row.find('.total_due').text(totalDue);
        $row.find('input[name="total_due[]"]').val(totalDue);
        
        // If there's an amount paid input, update remaining due
        if ($row.find('.amount_paid').length) {
            updateRemainingDue($row.find('.amount_paid'));
        }
    }
    
    // Function to update remaining due
    function updateRemainingDue(input) {
        const $input = $(input);
        const $row = $input.closest('.card');
        const amountPaid = parseFloat($input.val()) || 0;
        const totalDue = parseFloat($row.find('.total_due').text()) || 0;
        
        // Calculate remaining due
        const remainingDue = totalDue - amountPaid;
        const $container = $row.find('.remaining-due-container');
        
        // Update or create remaining due display
        if (remainingDue > 0) {
            // Update or create badge
            if ($container.find('.badge').length === 0) {
                $container.html('<span class="badge bg-danger">Remaining: ₹<span class="remaining-amount">' + remainingDue.toFixed(2) + '</span></span>');
            } else {
                $container.find('.remaining-amount').text(remainingDue.toFixed(2));
            }
        } else {
            // If fully paid, show paid badge
            $container.html('<span class="badge bg-success">Fully Paid</span>');
        }
    }
    
    // Listen for changes to electricity unit inputs
    $(document).on('input', '.electricity_unit', function() {
        updateTotals(this);
    });
    
    // Initialize calculations when the page loads
    $('.electricity_unit').each(function() {
        if ($(this).is(':visible') && $(this).is('input')) {
            updateTotals(this);
        }
    });
    
    // Update calculations when amount paid changes
    $(document).on('input', '.amount_paid', function() {
        updateRemainingDue(this);
    });
    
    // Add hover effect to payment cards
    $('.card-header').hover(
        function() {
            $(this).closest('.card').addClass('shadow');
        },
        function() {
            $(this).closest('.card').removeClass('shadow');
        }
    );
    
    // Add validation before form submission
    $('#rentForm').on('submit', function(e) {
        let valid = true;
        
        // Check if all visible electricity unit inputs have values
        $('.electricity_unit:visible').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Check if all visible amount paid inputs have values
        $('.amount_paid:visible').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill in all required fields before submitting.');
        }
    });
    
    // Add print functionality
    $('.print-receipt').on('click', function() {
        const $row = $(this).closest('.card');
        const month = $row.find('h5').text();
        const tenant = $('.tenant-name').first().text();
        const totalDue = $row.find('.total_due').text();
        const amountPaid = $row.find('.amount_paid').val() || $row.find('.amount_paid_display').data('amount-paid');
        
        const receiptContent = `
            <div style="font-family: Arial; max-width: 400px; padding: 20px; border: 1px solid #ccc;">
                <h2 style="text-align: center;">Rent Receipt</h2>
                <p><strong>Month:</strong> ${month}</p>
                <p><strong>Tenant:</strong> ${tenant}</p>
                <p><strong>Total Due:</strong> ₹${totalDue}</p>
                <p><strong>Amount Paid:</strong> ₹${amountPaid}</p>
                <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                <div style="margin-top: 50px; text-align: center;">
                    <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px; width: 200px;">
                        Signature
                    </div>
                </div>
            </div>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Rent Receipt - ${month}</title>
                </head>
                <body>
                    ${receiptContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        };
                    </script>
                </body>
            </html>
        `);
        printWindow.document.close();
    });
});
</script>