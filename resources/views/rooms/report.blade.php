@extends('layouts.app')

@section('page_title', 'Booking Report')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('content')
<div class="container-fluid" style="padding: 1.5rem; max-width: 1400px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--navy); margin: 0;">Meeting Room Booking Report</h2>
            <p style="color: var(--muted); margin: 0.25rem 0 0 0; font-size: 0.9rem;">View and export booking histories</p>
        </div>
        <div>
            <a href="{{ route('rooms.report.export', request()->all()) }}" class="btn btn-primary" style="background: var(--navy); border: none; padding: 0.6rem 1.2rem; font-weight: 600; border-radius: 8px;">
                <i class="fas fa-file-export" style="margin-right: 0.4rem;"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <form id="filterForm" method="GET" action="{{ route('rooms.report') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 0.4rem;">Date From</label>
                <input type="text" name="date_from" class="form-control flatpickr-date" value="{{ request('date_from') }}" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 0.4rem;">Date To</label>
                <input type="text" name="date_to" class="form-control flatpickr-date" value="{{ request('date_to') }}" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 0.4rem;">Status</label>
                <select name="status" class="form-select" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
                    <option value="">All Statuses</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 0.4rem;">Room</label>
                <select name="room_id" class="form-select" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 0.4rem;">Search Staff</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Staff ID..." class="form-control" style="background: var(--surface); color: var(--text); border: 1px solid var(--border); border-radius: 8px;">
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="background: var(--navy); border: none; padding: 0.6rem 1.2rem; font-weight: 600; border-radius: 8px; height: 38px;">
                    Filter
                </button>
                <a href="{{ route('rooms.report') }}" class="btn btn-secondary" style="background: var(--body-bg); color: var(--text); border: 1px solid var(--border); padding: 0.6rem 1.2rem; font-weight: 600; border-radius: 8px; height: 38px;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div class="table-responsive">
            <table class="table" style="margin: 0; width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead style="background: var(--table-head-bg);">
                    <tr>
                        <th style="padding: 1rem; font-size: 0.75rem; color: var(--table-head-color); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border);">Date / Time</th>
                        <th style="padding: 1rem; font-size: 0.75rem; color: var(--table-head-color); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border);">Room</th>
                        <th style="padding: 1rem; font-size: 0.75rem; color: var(--table-head-color); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border);">Booked By</th>
                        <th style="padding: 1rem; font-size: 0.75rem; color: var(--table-head-color); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border);">Purpose</th>
                        <th style="padding: 1rem; font-size: 0.75rem; color: var(--table-head-color); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr style="transition: background 0.2s;">
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                                <div style="font-weight: 700; color: var(--text);">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                                <div style="font-size: 0.8rem; color: var(--muted); margin-top: 2px;">
                                    <i class="far fa-clock" style="margin-right: 3px;"></i> 
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                                <div style="font-weight: 600; color: var(--text);">{{ $booking->room ? $booking->room->name : 'N/A' }}</div>
                            </td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                                <div style="font-weight: 600; color: var(--navy);">{{ $booking->user ? $booking->user->full_name : 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: var(--muted); margin-top: 2px;">{{ $booking->user ? $booking->user->username : '' }}</div>
                            </td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                                <div style="color: var(--text); font-size: 0.9rem;">{{ $booking->purpose }}</div>
                                <div style="font-size: 0.75rem; color: var(--muted); margin-top: 4px;">
                                    <i class="fas fa-users" style="margin-right: 3px;"></i> {{ $booking->attendee_count }} attendees
                                </div>
                            </td>
                            <td style="padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: top;">
                                @php
                                    $badgeColor = '#64748b'; $badgeBg = '#f1f5f9';
                                    if ($booking->status === 'APPROVED') { $badgeColor = '#16a34a'; $badgeBg = '#dcfce7'; }
                                    elseif ($booking->status === 'REJECTED' || $booking->status === 'CANCELLED') { $badgeColor = '#dc2626'; $badgeBg = '#fee2e2'; }
                                    elseif ($booking->status === 'PENDING') { $badgeColor = '#ca8a04'; $badgeBg = '#fef08a'; }
                                @endphp
                                <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 700; border-radius: 9999px; background: {{ $badgeBg }}; color: {{ $badgeColor }}; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ $booking->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 3rem; text-align: center; color: var(--muted);">
                                <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <p style="margin: 0; font-weight: 600;">No bookings found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border); background: var(--table-head-bg);">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        if (!form) return;
        
        const inputs = form.querySelectorAll('input:not(.flatpickr-date), select');
        let timeout = null;
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    form.submit();
                }, 400); 
            });
        });

        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (form) form.submit();
            }
        });
    });
</script>
@endsection




