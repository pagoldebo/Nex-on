<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Guard against running demo seeds in production (L-03)
        if (app()->environment('production')) {
            return;
        }

        $seedStaff = [
            [
                'name'              => 'Robert Hayes',
                'email'             => 'r.hayes@store.com',
                'phone'             => '+1 555-1001',
                'designation'       => 'Warehouse Manager',
                'department_name'   => 'Operations',
                'staff_sales'       => 0,
                'employment_status' => 'active',
                'created_at'        => '2022-01-15 09:00:00',
            ],
            [
                'name'              => 'Jessica Lee',
                'email'             => 'j.lee@store.com',
                'phone'             => '+1 555-1002',
                'designation'       => 'Store Manager',
                'department_name'   => 'Management',
                'staff_sales'       => 0,
                'employment_status' => 'active',
                'created_at'        => '2021-03-10 09:00:00',
            ],
            [
                'name'              => 'Tyler Brooks',
                'email'             => 't.brooks@store.com',
                'phone'             => '+1 555-1003',
                'designation'       => 'Sales Associate',
                'department_name'   => 'Sales',
                'staff_sales'       => 28400,
                'employment_status' => 'active',
                'created_at'        => '2023-09-01 09:00:00',
            ],
            [
                'name'              => 'Nadia Petrov',
                'email'             => 'n.petrov@store.com',
                'phone'             => '+1 555-1004',
                'designation'       => 'Cashier',
                'department_name'   => 'POS',
                'staff_sales'       => 14200,
                'employment_status' => 'active',
                'created_at'        => '2024-02-12 09:00:00',
            ],
            [
                'name'              => 'Kevin Park',
                'email'             => 'k.park@store.com',
                'phone'             => '+1 555-1005',
                'designation'       => 'Inventory Analyst',
                'department_name'   => 'Operations',
                'staff_sales'       => 0,
                'employment_status' => 'active',
                'created_at'        => '2023-06-20 09:00:00',
            ],
            [
                'name'              => 'Alicia Gomez',
                'email'             => 'a.gomez@store.com',
                'phone'             => '+1 555-1006',
                'designation'       => 'Customer Support',
                'department_name'   => 'Support',
                'staff_sales'       => 0,
                'employment_status' => 'on_leave',
                'created_at'        => '2022-11-05 09:00:00',
            ],
        ];

        foreach ($seedStaff as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);
            $user->name = $data['name'];
            $user->phone = $data['phone'];
            $user->designation = $data['designation'];
            $user->department_name = $data['department_name'];
            $user->staff_sales = $data['staff_sales'];
            $user->employment_status = $data['employment_status'];
            $user->is_staff = true;
            $user->role = 'staff';
            if (!$user->exists) {
                $user->password = Hash::make('password123');
                $user->created_at = $data['created_at'];
            }
            $user->save();

            // Find or create department safely
            $dept = Department::where('name', $data['department_name'])
                ->orWhere('name', 'like', "%{$data['department_name']}%")
                ->first();

            if (!$dept) {
                $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $data['department_name']), 0, 6));
                if (Department::where('code', $code)->exists()) {
                    $code .= rand(1, 99);
                }
                $dept = Department::create([
                    'name'      => $data['department_name'],
                    'code'      => $code,
                    'is_active' => true,
                ]);
            }

            if (!$user->departments()->where('departments.id', $dept->id)->exists()) {
                $user->departments()->attach($dept->id, ['role_title' => $data['designation'], 'is_primary' => true]);
            }
        }
    }
}
