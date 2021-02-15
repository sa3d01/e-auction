<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->enum('purchasing_type',['online','bank'])->default('online');
            $table->string('money')->default(0);
            $table->foreignId('user_id')->nullable();
            $table->enum('type',['wallet','package','purchasing_power','buy_item','refund_credit','refund_wallet','refund_purchasing_power'])->default('wallet');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('transfers');
    }
}
