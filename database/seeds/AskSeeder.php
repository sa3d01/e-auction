<?php

use Illuminate\Database\Seeder;
use \App\Ask;
class AskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ask::create([
            'ask->ar'=>'سؤال متكرر 1',
            'ask->en'=>'ask 1',
            'answer->ar'=>'نص الاجابة التعريفية',
            'answer->en'=>'answer 1'
        ]);
    }
}
