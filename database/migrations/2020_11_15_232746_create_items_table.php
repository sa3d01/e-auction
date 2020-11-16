<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('category_id')->default(1);
            $table->foreignId('mark_id')->nullable();
            $table->foreignId('model_id')->nullable();
            $table->string('model_class')->nullable();
            $table->string('factory')->nullable();
            $table->string('kms')->nullable();
            $table->foreignId('item_status_id')->nullable();
            $table->string('paper_image')->nullable();
            $table->foreignId('sale_type_id')->default(1);
            $table->string('price')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->json('location')->nullable();
            $table->enum('shipping_by',['user','app'])->default('app');
            $table->enum('status',['pending','rejected','shown','sold'])->default('pending');
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
        Schema::dropIfExists('items');
    }
}
