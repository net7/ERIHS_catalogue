<?php

namespace Tests;

use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RoleHasPermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MigrateFreshSeedOnce;
    use RefreshDatabase;
    use WithFaker;


    public function setUp(): void 
    {
        parent::setUp();
        (new PermissionsTableSeeder())->run();
        (new RolesTableSeeder())->run();
        (new RoleHasPermissionsTableSeeder())->run();
    }

}


