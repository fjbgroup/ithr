<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\IT\User as ITUser;

class ITStaffUserSeeder extends Seeder
{
    public function run(): void
    {
        $existingUsernames = ITUser::pluck('username')->map(fn($u) => strtolower(trim($u)))->flip();

        $staffList = Staff::with('department')->get();

        $created = 0;

        foreach ($staffList as $staff) {
            if ($existingUsernames->has(strtolower(trim($staff->staff_no)))) {
                continue;
            }

            ITUser::create([
                'username'             => $staff->staff_no,
                'full_name'            => $staff->name,
                'email'                => $staff->email,
                'password'             => Hash::make('password'),
                'role'                 => 'user',
                'department'           => $staff->staff_no,
                'dept_name'            => $staff->department?->name ?? '',
                'staff_id'             => $staff->staff_no,
                'is_active'            => true,
                'must_change_password' => false,
            ]);

            $created++;
        }

        $this->command->info("Created {$created} IT user accounts for staff.");
    }
}
