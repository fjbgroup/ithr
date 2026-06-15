<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\PasswordResetRequest;
use App\Models\IT\User;
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
        $allUsers  = User::orderBy('full_name')->get();

        $grouped = array_fill_keys($roleOrder, []);
        foreach ($allUsers as $u) {
            if (array_key_exists($u->role, $grouped)) $grouped[$u->role][] = $u;
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

    public function store(Request $request)
    {
        $request->validate([
            'username'   => ['required', 'string', 'min:3', 'max:50', Rule::unique(User::class, 'username')],
            'full_name'  => 'required|string|max:100',
            'password'   => 'required|string|min:6',
            'email'      => 'nullable|email|max:100',
            'role'       => 'required|in:admin,finance_admin,hou,gm,ceo,user',
            'department' => 'nullable|string|max:100',
            'dept_name'  => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'role'       => $request->role,
            'department' => $request->department,
            'dept_name'  => $request->dept_name,
            'is_active'  => 1,
        ]);

        ActivityLogService::log('CREATE', 'user', $user->id, 'Created user: '.$user->username);
        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'full_name'  => 'required|string|max:100',
            'email'      => 'nullable|email|max:100',
            'role'       => 'required|in:admin,finance_admin,hou,gm,ceo,user',
            'department' => 'nullable|string|max:100',
            'dept_name'  => 'nullable|string|max:100',
        ]);

        $user = User::findOrFail($id);
        $data = [
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'role'       => $request->role,
            'department' => $request->department,
            'dept_name'  => $request->dept_name,
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

