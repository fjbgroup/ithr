<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\ItPeripheralLoan;
use App\Models\IT\InventoryItem;
use App\Models\IT\AssetClass;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ItPeripheralLoanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('it')->user();
        
        $query = ItPeripheralLoan::with(['staff', 'inventoryItem', 'pickupVerifiedBy', 'endorsedBy'])->orderBy('created_at', 'desc');

        // If the user is not IT Admin, only show their own loans
        if (!$user->isAdmin()) {
            // Find staff record for this user
            $staff = Staff::where('email', $user->email)->first();
            if ($staff) {
                $query->where('staff_id', $staff->id);
            } else {
                // If they have no staff record, they have no loans
                $query->whereRaw('1 = 0');
            }
        }

        // Base query for stats to match user scope
        $statsQuery = ItPeripheralLoan::query();
        if (!$user->isAdmin()) {
            if ($staff) {
                $statsQuery->where('staff_id', $staff->id);
            } else {
                $statsQuery->whereRaw('1 = 0');
            }
        }

        $stat_total = (clone $statsQuery)->count();
        $stat_active = (clone $statsQuery)->where('status', 'Pending Return')->count();
        $stat_pending = (clone $statsQuery)->where('status', 'Pending Verification')->count();
        $stat_completed = (clone $statsQuery)->where('status', 'Completed')->count();

        // Apply Filters
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('asset_class')) {
            $query->whereHas('inventoryItem', function ($q) use ($request) {
                $q->where('asset_class', $request->asset_class);
            });
        }

        $loans = $query->paginate(15)->withQueryString();
        $allowedClasses = ['MONITOR', 'PC', 'LAPTOP', 'PRINTER', 'SCANNER', 'UPS', 'KEYBOARD', 'MOUSE', 'OTHER'];
        $assetClasses = AssetClass::whereIn('name', $allowedClasses)->orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('it.loans.index', compact('loans', 'assetClasses', 'departments', 'stat_total', 'stat_active', 'stat_pending', 'stat_completed'));
    }

    public function availableItems(Request $request)
    {
        $catId = $request->query('asset_class');
        
        if (!$catId) {
            return response()->json([]);
        }

        $items = InventoryItem::where('asset_class', $catId)
            ->where(function($q) {
                $q->where('item_status', 'Active')->orWhereNull('item_status');
            })
            ->get();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'loan_start_date' => 'required|date',
            'loan_end_date' => 'required|date|after_or_equal:loan_start_date',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::guard('it')->user();
        $staff = Staff::where('email', $user->email)->first();

        if (!$staff) {
            return back()->with('error', 'Staff record not found for your account.');
        }

        $item = InventoryItem::findOrFail($request->inventory_item_id);

        if ($item->item_status !== 'Active' && $item->item_status !== null) {
            return back()->with('error', 'The selected item is not available for loan.');
        }

        // Generate referral code: FGVJB/[DEPT]/[SEQUENCE]/[YEAR]
        $deptCode = $staff->department ? strtoupper(substr($staff->department->name, 0, 3)) : 'UNK';
        $year = date('Y');
        $count = ItPeripheralLoan::whereYear('created_at', $year)->count() + 1;
        $sequence = str_pad($count, 2, '0', STR_PAD_LEFT);
        
        $referralCode = "FGVJB/{$deptCode}/{$sequence}/{$year}";

        ItPeripheralLoan::create([
            'referral_code' => $referralCode,
            'staff_id' => $staff->id,
            'inventory_item_id' => $item->id,
            'loan_start_date' => $request->loan_start_date,
            'loan_end_date' => $request->loan_end_date,
            'status' => 'Pending Verification',
            'notes' => $request->notes,
        ]);

        \App\Services\IT\NotificationService::notifyAdminsWithEmail(
            'peripheral_loan',
            'New Peripheral Loan Request',
            $user->full_name . ' has submitted a new peripheral loan request (' . $referralCode . ').',
            route('it.loans.index')
        );

        \App\Services\IT\NotificationService::notifyUserWithEmail(
            $user->id,
            'peripheral_loan',
            'Peripheral Loan Request Submitted',
            'Your peripheral loan request (' . $referralCode . ') has been successfully submitted and is pending verification.',
            route('it.loans.index')
        );

        return redirect()->route('it.loans.index')->with('success', 'Loan application submitted successfully.');
    }

    public function verifyPickup($id)
    {
        $loan = ItPeripheralLoan::findOrFail($id);

        if ($loan->status !== 'Pending Verification') {
            return back()->with('error', 'Invalid loan status.');
        }

        $loan->update([
            'status' => 'Pending Return',
            'pickup_verified_at' => Carbon::now(),
            'pickup_verified_by' => Auth::guard('it')->id(),
        ]);

        // Change inventory item status
        if ($loan->inventoryItem) {
            $loan->inventoryItem->update(['item_status' => 'Loaned']);
        }

        $staff = \App\Models\Staff::find($loan->staff_id);
        if ($staff && $staff->email) {
            $requesterUser = \App\Models\IT\User::where('email', $staff->email)->first();
            if ($requesterUser) {
                \App\Services\IT\NotificationService::notifyUserWithEmail(
                    $requesterUser->id,
                    'peripheral_loan',
                    'Peripheral Loan Verified',
                    'Your peripheral loan (' . $loan->referral_code . ') has been verified and picked up.',
                    route('it.loans.index')
                );
            }
        }

        \App\Services\IT\NotificationService::notifyAdminsWithEmail(
            'peripheral_loan',
            'Peripheral Loan Verified',
            'Peripheral loan (' . $loan->referral_code . ') has been verified by ' . Auth::guard('it')->user()->full_name . '.',
            route('it.loans.index')
        );

        return back()->with('success', 'Pickup verified. Item status updated to Loaned.');
    }

    public function userReturn($id)
    {
        $loan = ItPeripheralLoan::findOrFail($id);

        if ($loan->status !== 'Pending Return') {
            return back()->with('error', 'Invalid loan status.');
        }

        $user = Auth::guard('it')->user();
        $staff = Staff::where('email', $user->email)->first();
        
        if (!$user->isAdmin() && ($staff && $loan->staff_id !== $staff->id)) {
            return back()->with('error', 'Unauthorized action.');
        }

        $loan->update([
            'status' => 'Pending Endorse',
            'return_verified_at' => Carbon::now(),
        ]);

        \App\Services\IT\NotificationService::notifyUserWithEmail(
            $user->id,
            'peripheral_loan',
            'Peripheral Loan Return Initiated',
            'You have initiated the return process for peripheral loan (' . $loan->referral_code . '). Please hand the item to IT.',
            route('it.loans.index')
        );

        \App\Services\IT\NotificationService::notifyAdminsWithEmail(
            'peripheral_loan',
            'Peripheral Loan Return Initiated',
            $user->full_name . ' has initiated the return for peripheral loan ' . $loan->referral_code . '.',
            route('it.loans.index')
        );

        return back()->with('success', 'Return process initiated. Please hand the item to IT.');
    }

    public function endorseReturn($id)
    {
        $loan = ItPeripheralLoan::findOrFail($id);

        if ($loan->status !== 'Pending Endorse' && $loan->status !== 'Pending Return') {
            return back()->with('error', 'Invalid loan status.');
        }

        $loan->update([
            'status' => 'Completed',
            'endorsed_at' => Carbon::now(),
            'endorsed_by' => Auth::guard('it')->id(),
        ]);

        // Change inventory item status back to Active
        if ($loan->inventoryItem) {
            $loan->inventoryItem->update(['item_status' => 'Active']);
        }

        $staff = \App\Models\Staff::find($loan->staff_id);
        if ($staff && $staff->email) {
            $requesterUser = \App\Models\IT\User::where('email', $staff->email)->first();
            if ($requesterUser) {
                \App\Services\IT\NotificationService::notifyUserWithEmail(
                    $requesterUser->id,
                    'peripheral_loan',
                    'Peripheral Loan Return Endorsed',
                    'Your return for peripheral loan (' . $loan->referral_code . ') has been endorsed and completed.',
                    route('it.loans.index')
                );
            }
        }

        \App\Services\IT\NotificationService::notifyAdminsWithEmail(
            'peripheral_loan',
            'Peripheral Loan Return Endorsed',
            'Peripheral loan return (' . $loan->referral_code . ') has been endorsed by ' . Auth::guard('it')->user()->full_name . '.',
            route('it.loans.index')
        );

        return back()->with('success', 'Return endorsed. Item is now Active.');
    }

    public function receipt($id)
    {
        $loan = ItPeripheralLoan::with(['staff.department', 'inventoryItem', 'pickupVerifiedBy', 'endorsedBy'])->findOrFail($id);

        if ($loan->status !== 'Completed') {
            return back()->with('error', 'Receipt is only available for completed loans.');
        }

        return view('it.loans.receipt', compact('loan'));
    }
}
