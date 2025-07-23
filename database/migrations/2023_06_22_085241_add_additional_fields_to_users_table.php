<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('users', function (Blueprint $table) {

            $connection = DB::connection();
            $dbHandle = $connection->getPdo();

            $table->text('surname')
                ->after('name')
                ->nullable();

            if ('sqlite' === $dbHandle->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
                $table->string('full_name')->nullable();
            } else {
                $table->string('full_name')->virtualAs('concat(name, \' \', surname)');
            }

            //     $table->text('nationality')
            //             ->after('surname')
            //             ->nullable();
            $table->text('birth_year')
                ->after('full_name')
                ->nullable();
            $table->string('gender')
                ->after('birth_year')
                ->nullable();
            $table->text('home_institution')
                ->after('gender')
                ->nullable();
            $table->text('home_institution_id')
                ->after('home_institution')
                ->nullable();
            $table->text('institution_address')
                ->after('home_institution')
                ->nullable();
            $table->text('institution_city')
                ->after('institution_address')
                ->nullable();
            $table->string('institution_status_code')
                ->after('institution_city')
                ->nullable();
            //     $table->text('institution_country')
            //             ->after('institution_status_code')
            //             ->nullable();
            $table->text('job')
                ->after('institution_status_code')
                ->nullable();
            $table->text('academic_background')
                ->after('job')
                ->nullable();
            $table->string('position')
                ->after('academic_background')
                ->nullable();
            $table->text('office_phone')
                ->after('position')
                ->nullable();
            $table->text('mobile_phone')
                ->after('office_phone')
                ->nullable();
            $table->text('city')
                ->after('surname')
                ->nullable();
            $table->text('country')
                ->after('city')
                ->nullable();
            $table->text('mailing_address')
                ->after('position')
                ->nullable();
            $table->text('short_cv')->nullable();
            $table->boolean('complete_profile')
                ->default(false);
            $table->boolean('first_login')
                ->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
