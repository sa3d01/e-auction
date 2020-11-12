<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')->nullable();
            $table->char('name',50)->nullable();
            $table->json('phone_details')->nullable();
            $table->char('phone',15)->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->char('email',50)->unique()->nullable();
            //for id and type --fcm
            $table->json('device')->nullable();
            $table->char('activation_code',6)->nullable();
            $table->integer('status')->default(1);
            $table->boolean('online')->default(1);
            $table->char('image',20)->nullable();
            $table->string('password')->nullable();
            $table->json('location')->nullable();
            $table->json('more_details')->nullable();
            $table->softDeletes('deleted_at', 0);
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
        Schema::dropIfExists('users');
    }
}
