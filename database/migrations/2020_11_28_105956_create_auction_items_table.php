<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->nullable();
            $table->foreignId('auction_id')->nullable();
            $table->integer('price')->default(0);
            $table->integer('latest_charge')->default(0);
            $table->integer('start_date')->nullable();
            $table->enum('vip',['true','false'])->default('false');
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
        Schema::dropIfExists('auction_items');
    }
}
