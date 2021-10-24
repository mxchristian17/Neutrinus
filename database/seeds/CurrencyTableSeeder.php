<?php

use Illuminate\Database\Seeder;
use App\Currency;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency = new Currency();
        $currency->name = 'Dolares estadounidenses';
        $currency->prefix = 'US$';
        $currency->author_id = 1;
        $currency->updater_id = 1;
        $currency->save();

        $currency = new Currency();
        $currency->name = 'Pesos Argentinos';
        $currency->prefix = 'AR$';
        $currency->author_id = 1;
        $currency->updater_id = 1;
        $currency->save();

        $currency = new Currency();
        $currency->name = 'Euros';
        $currency->prefix = '€';
        $currency->author_id = 1;
        $currency->updater_id = 1;
        $currency->save();

        $currency = new Currency();
        $currency->name = 'Yuanes';
        $currency->prefix = 'CN¥';
        $currency->author_id = 1;
        $currency->updater_id = 1;
        $currency->save();
    }
}
