<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UnifiedAuthSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure staff 0000001 is admin_it with IT and WT access
        DB::table('users')
            ->where('staff_no', '0000001')
            ->update([
                'role'     => 'admin_it',
                'it_role'  => 'admin_it',
                'wt_role'  => 'admin_it',
                'is_active' => 1,
            ]);

        // Set all users' passwords to bcrypt(staff_no) — default credential
        // Admins keep must_change_password = false; everyone else must change on first login
        $users = DB::table('users')->select('id', 'staff_no', 'role')->get();

        foreach ($users as $user) {
            $isAdmin = in_array($user->role, ['admin_it', 'admin', 'admin_hr']);
            DB::table('users')->where('id', $user->id)->update([
                'password'             => Hash::make($user->staff_no),
                'must_change_password' => $isAdmin ? 0 : 1,
            ]);
        }

        // Admin 0000001 uses "password" as credential (not their staff number)
        DB::table('users')
            ->where('staff_no', '0000001')
            ->update([
                'password'             => Hash::make('password'),
                'must_change_password' => 0,
            ]);

        $this->command->info('✓ Set ' . count($users) . ' user passwords to their staff number.');
        $this->command->info('✓ Staff 0000001 set as admin_it with password "password".');
    }
}
