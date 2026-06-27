<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\PasswordResetRequest;
use App\Models\IT\User;
use App\Models\Staff;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleOrder  = ['ceo','gm','hou','admin','finance_admin','user'];
        $activeRole = $request->get('role_tab', 'ceo');
        $search     = trim($request->get('search', ''));

        // All IT users — for stats chips and rail counts
        $allUsers      = User::whereNotNull('it_role')->get();
        $totalUsers    = $allUsers->count();
        $activeCount   = $allUsers->where('is_active', true)->count();
        $inactiveCount = $totalUsers - $activeCount;

        $roleCounts = array_fill_keys($roleOrder, 0);
        foreach ($allUsers as $u) {
            $key = $u->it_role === 'admin_it' ? 'admin' : $u->it_role;
            if (array_key_exists($key, $roleCounts)) $roleCounts[$key]++;
        }

        // Paginated query for the active role panel
        $query = User::whereNotNull('it_role');
        if ($activeRole === 'admin') {
            $query->whereIn('it_role', ['admin_it', 'admin']);
        } else {
            $query->where('it_role', $activeRole);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name',      'like', "%$search%")
                  ->orWhere('staff_no', 'like', "%$search%")
                  ->orWhere('email',    'like', "%$search%")
                  ->orWhere('dept_name','like', "%$search%");
            });
        }
        $users = $query->orderBy('name')->paginate(8)->appends($request->except(['partial']));

        if ($request->boolean('partial')) {
            $html = view('it.users.partials.user-list', [
                'users'      => $users,
                'activeRole' => $activeRole,
                'search'     => $search,
            ])->render();
            return response()->json(['html' => $html, 'total' => $users->total()]);
        }

        $resetRequests = PasswordResetRequest::orderBy('requested_at', 'desc')->limit(50)->get();
        $pendingCount  = $resetRequests->where('status', 'pending')->count();

        $editUser = null;
        if ($request->get('action') === 'edit' && $request->get('id')) {
            $editUser = User::find((int) $request->get('id'));
        }

        return view('it.users.index', compact(
            'roleCounts', 'totalUsers', 'activeCount', 'inactiveCount',
            'resetRequests', 'pendingCount', 'editUser',
            'users', 'activeRole', 'search'
        ));
    }

    public function staffSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Staff::with('department')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('staff_no', 'like', "%{$q}%");
            })
            ->where('is_active', 1)
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn($s) => [
                'staff_no'  => $s->staff_no,
                'name'      => $s->name,
                'email'     => $s->email ?? '',
                'dept_name' => $s->department?->name ?? '',
                'position'  => $s->position ?? '',
            ]);

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|min:2|max:50',
            'full_name' => 'required|string|max:100',
            'email'     => 'nullable|email|max:100',
            'role'      => 'required|in:admin,admin_it,finance_admin,hou,gm,ceo,user',
            'dept_name' => 'nullable|string|max:200',
        ]);

        $staffNo  = trim($request->username);
        $existing = User::whereRaw('TRIM(staff_no) = ?', [$staffNo])->first();

        if ($existing) {
            // Every staff defaults to the "Staff" IT role, so an existing account
            // already has an it_role. Treat "Add" as an upsert: set/elevate the
            // requested role. Their HR role and WT role remain unchanged.
            $data = ['it_role' => $request->role];
            if ($request->filled('dept_name'))  $data['dept_name']  = $request->dept_name;
            if ($request->filled('email'))       $data['email']      = $request->email;
            if ($request->filled('password'))    $data['password']   = Hash::make($request->password);

            $existing->update($data);
            ActivityLogService::log('UPDATE', 'user', $existing->id, 'Set IT role to '.$request->role.' for: '.$existing->username);

            $roleTab = $request->role === 'admin_it' ? 'admin' : $request->role;
            return redirect()->route('it.users.index', ['role_tab' => $roleTab])
                ->with('success', $existing->full_name.' is now '.$existing->getItRoleLabel().' in the IT system.');
        }

        // Brand-new user — password is required
        $request->validate(['password' => 'required|string|min:6']);

        $user = User::create([
            'username'  => $staffNo,
            'password'  => Hash::make($request->password),
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'role'      => 'staff',
            'it_role'   => $request->role,
            'dept_name' => $request->dept_name,
            'is_active' => 1,
        ]);

        ActivityLogService::log('CREATE', 'user', $user->id, 'Created IT user: '.$user->username);
        $roleTab = $request->role === 'admin_it' ? 'admin' : $request->role;
        return redirect()->route('it.users.index', ['role_tab' => $roleTab])
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'nullable|email|max:100',
            'role'      => 'required|in:admin,admin_it,finance_admin,hou,gm,ceo,user',
            'dept_name' => 'nullable|string|max:200',
        ]);

        $user = User::findOrFail($id);
        $data = [
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'it_role'   => $request->role,
            'dept_name' => $request->dept_name,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        ActivityLogService::log('UPDATE', 'user', $id, 'Updated user: '.$user->username);
        return back()->with('success', 'User updated.');
    }

    public function destroy(int $id)
    {
        if ($id === Auth::guard('it')->id()) return back()->with('error', 'Cannot change your own IT role.');
        $user = User::findOrFail($id);
        // Policy: all staff keep at least the default "Staff" role so they can log
        // in. "Revoke" demotes an elevated user back to Staff rather than removing access.
        $user->update(['it_role' => 'user']);
        ActivityLogService::log('UPDATE', 'user', $id, 'Reset IT role to Staff for: '.$user->username);
        return back()->with('success', $user->full_name.' reset to Staff role.');
    }

    public function toggle(int $id)
    {
        if ($id === Auth::guard('it')->id()) return back()->with('error', 'Cannot toggle your own account.');
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        ActivityLogService::log('USER_TOGGLE', 'user', $id, ($user->is_active ? 'Activated' : 'Deactivated').' user: '.$user->username);
        return back()->with('success', 'User '.($user->is_active ? 'activated' : 'deactivated').'.');
    }

    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make('password'), 'must_change_password' => true]);
        PasswordResetRequest::where('user_id', $id)->where('status', 'pending')->update(['status' => 'resolved', 'resolved_at' => now(), 'resolved_by' => Auth::guard('it')->id()]);
        ActivityLogService::log('PASSWORD_RESET', 'user', $id, 'Admin reset password to default for: '.$user->username);
        return back()->with('success', 'Password reset to default.');
    }

    public function rejectReset(int $id)
    {
        PasswordResetRequest::findOrFail($id)->update(['status' => 'rejected', 'resolved_at' => now(), 'resolved_by' => Auth::guard('it')->id()]);
        ActivityLogService::log('RESET_REJECTED', 'user', 0, 'Admin rejected password reset request #'.$id);
        return back()->with('success', 'Reset request rejected.');
    }
}

