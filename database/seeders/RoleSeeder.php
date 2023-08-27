<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert(
            [
                ['title'=>'Admin', 'discription'=>'Creator of this site', 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()],
            ]
        );
    }
}
