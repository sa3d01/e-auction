<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('name',50)->nullable();
            $table->char('email',50)->unique()->nullable();
            $table->char('mobile',15)->unique()->nullable();
            $table->char('image',20)->nullable();
            $table->string('password')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('user_type_id')->default(2)->constrained();
            $table->rememberToken();
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
        Schema::dropIfExists('admins');
    }
}
