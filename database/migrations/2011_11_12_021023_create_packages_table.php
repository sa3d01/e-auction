<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->char('image',20)->nullable();
            $table->json('name')->nullable();
            //arrays of objects included in language label
            $table->json('note')->nullable();
            $table->double('price')->default(0.0);
            //one month
            $table->integer('period')->default(1);
            //purchasing_power_increase value not percent
            $table->integer('purchasing_power_increase')->default(0);
            $table->integer('paid_files_count')->default(0);
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
        Schema::dropIfExists('packages');
    }
}
