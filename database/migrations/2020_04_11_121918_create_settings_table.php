<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('about')->nullable();
            $table->json('licence')->nullable();
            $table->json('private')->nullable();
            $table->json('languages')->nullable();
            $table->json('socials')->nullable();
            $table->json('contacts')->nullable();
            $table->json('app_links')->nullable();
            //ratio,...
            $table->json('more_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
