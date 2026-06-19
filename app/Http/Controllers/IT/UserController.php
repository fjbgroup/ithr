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
        $roleOrder = ['ceo','gm','hou','admin','finance_admin','user'];

        // Only show IT-relevant roles; admin_it is normalised into the 'admin' bucket
        $itRoles  = ['admin_it','admin','finance_admin','hou','gm','ceo','user'];
        $allUsers = User::whereIn('role', $itRoles)->orderBy('name')->get();

        $grouped = array_fill_keys($roleOrder, []);
        foreach ($allUsers as $u) {
            $key = $u->role === 'admin_it' ? 'admin' : $u->role;
            if (array_key_exists($key, $grouped)) $grouped[$key][] = $u;
        }

        $totalUsers    = $allUsers->count();
        $activeCount   = $allUsers->where('is_active', true)->count();
        $inactiveCount = $totalUsers - $activeCount;

        $resetRequests = PasswordResetRequest::orderBy('requested_at', 'desc')->limit(50)->get();
        $pendingCount  = $resetRequests->where('status', 'pending')->count();

        $editUser = null;
        if ($request->get('action') === 'edit' && $request->get('id')) {
            $editUser = User::find((int) $request->get('id'));
        }

        return view('it.users.index', compact(
            'grouped', 'totalUsers', 'activeCount', 'inactiveCount',
            'resetRequests', 'pendingCount', 'editUser'
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
        $itRoles  = ['admin_it','admin','finance_admin','hou','gm','ceo','user'];
        $existing = User::whereRaw('TRIM(staff_no) = ?', [$staffNo])->first();

        if ($existing) {
            if (in_array($existing->role, $itRoles)) {
                // Already has IT access — nothing to do
                return back()
                    ->withInput()
                    ->withErrors(['username' => $existing->full_name.' already has IT system access (role: '.$existing->roleName().').']);
            }

            // HR-only staff — grant IT access by updating their role.
            // They keep their existing password so HR login is unaffected.
            $data = ['role' => $request->role];
            if ($request->filled('dept_name'))  $data['dept_name']  = $request->dept_name;
            if ($request->filled('email'))       $data['email']      = $request->email;
            if ($request->filled('password'))    $data['password']   = Hash::make($request->password);

            $existing->update($data);
            ActivityLogService::log('UPDATE', 'user', $existing->id, 'Granted IT access (role: '.$request->role.') to: '.$existing->username);

            $roleTab = $request->role === 'admin_it' ? 'admin' : $request->role;
            return redirect()->route('it.users.index', ['role_tab' => $roleTab])
                ->with('success', 'IT access granted to '.$existing->full_name.' as '.$existing->roleName().'.');
        }

        // Brand-new user — password is required
        $request->validate(['password' => 'required|string|min:6']);

        $user = User::create([
            'username'  => $staffNo,
            'password'  => Hash::make($request->password),
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'role'      => $request->role,
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
            'role'      => $request->role,
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
        if ($id === Auth::guard('it')->id()) return back()->with('error', 'Cannot delete your own account.');
        $user = User::findOrFail($id);
        ActivityLogService::log('DELETE', 'user', $id, 'Deleted user: '.$user->username);
        $user->delete();
        return back()->with('success', 'User deleted.');
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

