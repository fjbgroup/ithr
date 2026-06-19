<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\User;

class StaffUserSeeder extends Seeder
{
    public function run(): void
    {
        $staffList = Staff::whereDoesntHave('user')->get();

        foreach ($staffList as $staff) {
            User::create([
                'staff_no'      => $staff->staff_no,
                'name'          => $staff->name,
                'email'         => $staff->email,
                'password'      => Hash::make('password'),
                'role'          => 'staff',
                'staff_id'      => $staff->id,
                'department_id' => $staff->department_id,
                'position'      => $staff->position,
                'company'       => $staff->company ?? 'FJB',
                'is_active'     => true,
            ]);
        }

        $this->command->info("Created user accounts for {$staffList->count()} staff.");
    }
}
