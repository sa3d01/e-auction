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
            $table->json('privacy')->nullable();
            $table->json('languages')->nullable();
            $table->json('socials')->nullable();
            $table->json('contacts')->nullable();
            $table->json('purchasing_power_text')->nullable();
            //in seconds
            $table->integer('auction_period')->default(60);
            $table->integer('auction_increasing_period')->default(60);
            $table->integer('negotiation_period')->default(360);
            $table->integer('bid_pause_period')->default(360);
            //علي الشاري
            $table->integer('finish_papers')->default(0);
            $table->integer('tax_ratio')->default(60);
            $table->integer('owner_tax_ratio')->default(15);
            $table->integer('app_ratio')->default(2);
            $table->integer('add_item_tax')->default(60);
            //نسبة بتضرب فى القوة الشرائية والنتيجة يقدر يزايد بيها
            $table->integer('purchasing_power_ratio')->default(60);
            $table->string('app_version')->nullable();
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
