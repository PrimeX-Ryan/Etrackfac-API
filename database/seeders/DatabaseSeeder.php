<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Requirement;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Departments
        $depts = [
            'Computer Science',
            'Information Technology',
            'Information Systems',
            'Associate in Computer Technology',
            'Civil Engineering',
            'Architecture'
        ];

        $deptModels = [];
        foreach ($depts as $dept) {
            $deptModels[$dept] = Department::firstOrCreate(['name' => $dept]);
        }

        // 2. Create Roles
        $roles = ['admin', 'dean', 'program_chair', 'faculty'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 3. Create Requirements
        $requirements = [
            ['name' => 'Course Syllabus', 'description' => 'Approved syllabus for the current semester', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Class Record', 'description' => 'Official class record with attendance', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Table of Specifications (Midterm)', 'description' => 'TOS for Midterm Examinations', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Midterm Exam Questions', 'description' => 'Copy of Midterm Exam', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Table of Specifications (Finals)', 'description' => 'TOS for Final Examinations', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Final Exam Questions', 'description' => 'Copy of Final Exam', 'semester' => '1st Semester 2023-2024'],
            ['name' => 'Grade Sheet', 'description' => 'Final Grade Sheet submitted to registrar', 'semester' => '1st Semester 2023-2024'],
        ];

        foreach ($requirements as $req) {
            Requirement::firstOrCreate([
                'name' => $req['name']
            ], [
                'description' => $req['description'],
                'semester' => $req['semester'],
                'is_required' => true
            ]);
        }

        // 4. Create Users

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@etrack.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved' // Auto-approve seeder users
            ]
        );
        $admin->assignRole('admin');

        // Dean
        $dean = User::firstOrCreate(
            ['email' => 'dean@etrack.com'],
            [
                'name' => 'Dr. Dean Valros',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved'
            ]
        );
        $dean->assignRole('dean');

        // CS Chair
        $csChair = User::firstOrCreate(
            ['email' => 'chair.cs@etrack.com'],
            [
                'name' => 'Chair Alan Turing',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved'
            ]
        );
        $csChair->assignRole('program_chair');

        // IT Chair
        $itChair = User::firstOrCreate(
            ['email' => 'chair.it@etrack.com'],
            [
                'name' => 'Chair Ada Lovelace',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Information Technology']->id,
                'status' => 'approved'
            ]
        );
        $itChair->assignRole('program_chair');

        // Faculty Members
        $faculty1 = User::firstOrCreate(
            ['email' => 'faculty.cs@etrack.com'],
            [
                'name' => 'Prof. John Doe',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved'
            ]
        );
        $faculty1->assignRole('faculty');

        $faculty2 = User::firstOrCreate(
            ['email' => 'faculty.it@etrack.com'],
            [
                'name' => 'Prof. Jane Smith',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Information Technology']->id,
                'status' => 'approved'
            ]
        );
        $faculty2->assignRole('faculty');
        
         // Legacy Generic Users (Keep for compatibility if you used them before)
         $legacyFaculty = User::firstOrCreate(
            ['email' => 'faculty@etrack.com'],
            [
                'name' => 'Generic Faculty',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved'
            ]
        );
        $legacyFaculty->assignRole('faculty');

        $legacyChair = User::firstOrCreate(
            ['email' => 'chair@etrack.com'],
            [
                'name' => 'Generic Chair',
                'password' => Hash::make('password'),
                'department_id' => $deptModels['Computer Science']->id,
                'status' => 'approved'
            ]
        );
        $legacyChair->assignRole('program_chair');

        // 5. Run Sample Data Seeder for test submissions
        $this->call(SampleDataSeeder::class);
    }
}
