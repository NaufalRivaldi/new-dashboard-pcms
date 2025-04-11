<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeployProduction extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addRoles();
        $this->addPermissions();
        $this->syncRolesAndPermissions();

        $users = [
            [
                'name' => 'Naufal Rivaldi',
                'email' => 'naufal.rivaldi33@gmail.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'status' => true,
            ],
        ];

        foreach ($users as $user) {
            $user = User::create($user);

            $user->assignRole(['super_admin']);
        }
    }

    private function addRoles(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Owner',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Approver',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Submitter',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    private function addPermissions(): void
    {
        $permissions = [
            ['name' => 'view_activity', 'guard_name' => 'web',],
            ['name' => 'view_any_activity', 'guard_name' => 'web',],
            ['name' => 'create_activity', 'guard_name' => 'web',],
            ['name' => 'update_activity', 'guard_name' => 'web',],
            ['name' => 'replicate_activity', 'guard_name' => 'web',],
            ['name' => 'reorder_activity', 'guard_name' => 'web',],
            ['name' => 'delete_activity', 'guard_name' => 'web',],
            ['name' => 'delete_any_activity', 'guard_name' => 'web',],
            ['name' => 'force_delete_activity', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_activity', 'guard_name' => 'web',],
            ['name' => 'view_branch', 'guard_name' => 'web',],
            ['name' => 'view_any_branch', 'guard_name' => 'web',],
            ['name' => 'create_branch', 'guard_name' => 'web',],
            ['name' => 'update_branch', 'guard_name' => 'web',],
            ['name' => 'replicate_branch', 'guard_name' => 'web',],
            ['name' => 'reorder_branch', 'guard_name' => 'web',],
            ['name' => 'delete_branch', 'guard_name' => 'web',],
            ['name' => 'delete_any_branch', 'guard_name' => 'web',],
            ['name' => 'force_delete_branch', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_branch', 'guard_name' => 'web',],
            ['name' => 'view_education', 'guard_name' => 'web',],
            ['name' => 'view_any_education', 'guard_name' => 'web',],
            ['name' => 'create_education', 'guard_name' => 'web',],
            ['name' => 'update_education', 'guard_name' => 'web',],
            ['name' => 'replicate_education', 'guard_name' => 'web',],
            ['name' => 'reorder_education', 'guard_name' => 'web',],
            ['name' => 'delete_education', 'guard_name' => 'web',],
            ['name' => 'delete_any_education', 'guard_name' => 'web',],
            ['name' => 'force_delete_education', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_education', 'guard_name' => 'web',],
            ['name' => 'view_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'create_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'update_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'delete_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::active::student', 'guard_name' => 'web',],
            ['name' => 'view_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'create_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'update_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'delete_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::active::student::education', 'guard_name' => 'web',],
            ['name' => 'view_imported::fee', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::fee', 'guard_name' => 'web',],
            ['name' => 'create_imported::fee', 'guard_name' => 'web',],
            ['name' => 'update_imported::fee', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::fee', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::fee', 'guard_name' => 'web',],
            ['name' => 'delete_imported::fee', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::fee', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::fee', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::fee', 'guard_name' => 'web',],
            ['name' => 'view_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'create_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'update_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'delete_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::inactive::student', 'guard_name' => 'web',],
            ['name' => 'view_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'create_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'update_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'delete_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::leave::student', 'guard_name' => 'web',],
            ['name' => 'view_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'view_any_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'create_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'update_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'replicate_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'reorder_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'delete_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'delete_any_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_imported::new::student', 'guard_name' => 'web',],
            ['name' => 'view_lesson', 'guard_name' => 'web',],
            ['name' => 'view_any_lesson', 'guard_name' => 'web',],
            ['name' => 'create_lesson', 'guard_name' => 'web',],
            ['name' => 'update_lesson', 'guard_name' => 'web',],
            ['name' => 'replicate_lesson', 'guard_name' => 'web',],
            ['name' => 'reorder_lesson', 'guard_name' => 'web',],
            ['name' => 'delete_lesson', 'guard_name' => 'web',],
            ['name' => 'delete_any_lesson', 'guard_name' => 'web',],
            ['name' => 'force_delete_lesson', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_lesson', 'guard_name' => 'web',],
            ['name' => 'view_region', 'guard_name' => 'web',],
            ['name' => 'view_any_region', 'guard_name' => 'web',],
            ['name' => 'create_region', 'guard_name' => 'web',],
            ['name' => 'update_region', 'guard_name' => 'web',],
            ['name' => 'replicate_region', 'guard_name' => 'web',],
            ['name' => 'reorder_region', 'guard_name' => 'web',],
            ['name' => 'delete_region', 'guard_name' => 'web',],
            ['name' => 'delete_any_region', 'guard_name' => 'web',],
            ['name' => 'force_delete_region', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_region', 'guard_name' => 'web',],
            ['name' => 'view_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'view_any_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'create_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'update_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'replicate_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'reorder_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'delete_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'delete_any_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'force_delete_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_report::branch::not::imported::data', 'guard_name' => 'web',],
            ['name' => 'view_role', 'guard_name' => 'web',],
            ['name' => 'view_any_role', 'guard_name' => 'web',],
            ['name' => 'create_role', 'guard_name' => 'web',],
            ['name' => 'update_role', 'guard_name' => 'web',],
            ['name' => 'delete_role', 'guard_name' => 'web',],
            ['name' => 'delete_any_role', 'guard_name' => 'web',],
            ['name' => 'view_summary', 'guard_name' => 'web',],
            ['name' => 'view_any_summary', 'guard_name' => 'web',],
            ['name' => 'create_summary', 'guard_name' => 'web',],
            ['name' => 'update_summary', 'guard_name' => 'web',],
            ['name' => 'replicate_summary', 'guard_name' => 'web',],
            ['name' => 'reorder_summary', 'guard_name' => 'web',],
            ['name' => 'delete_summary', 'guard_name' => 'web',],
            ['name' => 'delete_any_summary', 'guard_name' => 'web',],
            ['name' => 'force_delete_summary', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_summary', 'guard_name' => 'web',],
            ['name' => 'view_user', 'guard_name' => 'web',],
            ['name' => 'view_any_user', 'guard_name' => 'web',],
            ['name' => 'create_user', 'guard_name' => 'web',],
            ['name' => 'update_user', 'guard_name' => 'web',],
            ['name' => 'replicate_user', 'guard_name' => 'web',],
            ['name' => 'reorder_user', 'guard_name' => 'web',],
            ['name' => 'delete_user', 'guard_name' => 'web',],
            ['name' => 'delete_any_user', 'guard_name' => 'web',],
            ['name' => 'force_delete_user', 'guard_name' => 'web',],
            ['name' => 'force_delete_any_user', 'guard_name' => 'web',],
            ['name' => 'page_Analysis', 'guard_name' => 'web',],
            ['name' => 'page_Compare', 'guard_name' => 'web',],
            ['name' => 'page_TopOrUnderFive', 'guard_name' => 'web',],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }

    private function syncRolesAndPermissions(): void
    {
        $permissions = Permission::get();

        foreach ($permissions as $permission) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => 1,
            ]);
        }
    }
}
