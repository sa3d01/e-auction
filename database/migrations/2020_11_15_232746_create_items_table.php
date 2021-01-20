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
            $table->json('images')->nullable();
            $table->foreignId('category_id')->default(1);
            $table->foreignId('mark_id')->nullable();
            $table->foreignId('model_id')->nullable();
            $table->foreignId('item_status_id')->nullable();
            $table->integer('sunder_count')->nullable();
            $table->foreignId('fetes_id')->nullable();
            $table->foreignId('color_id')->nullable();
            $table->integer('kms_count')->nullable();
            $table->foreignId('scan_status_id')->nullable();
            $table->foreignId('paper_status_id')->nullable();
            $table->string('paper_image')->nullable();
            $table->foreignId('auction_type_id')->default(1);
            $table->integer('price')->nullable();
            $table->integer('auction_price')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->json('location')->nullable();
            $table->json('more_details')->nullable();
            $table->enum('shipping_by',['user','app'])->default('app');
            //سجل نظام ضريبي
            $table->enum('tax',['true','false'])->default('false');
            //pay throw item
            $table->enum('status',['pending','accepted','rejected','shown','sold'])->default('pending');
            $table->boolean('pay_status')->default(0);
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
