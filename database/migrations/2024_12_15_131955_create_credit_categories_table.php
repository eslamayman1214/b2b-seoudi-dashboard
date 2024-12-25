<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCreditCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('credit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->integer('value')->unsigned();
            $table->timestamps();
        });

        // Insert default categories
        DB::table('credit_categories')->insert([
            ['category_name' => 'Bronze', 'value' => 1000],
            ['category_name' => 'Silver', 'value' => 2000],
            ['category_name' => 'Golden', 'value' => 3000],
            ['category_name' => 'Platinum', 'value' => 4000],
            ['category_name' => 'Diamond', 'value' => 5000],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('credit_categories');
    }
}
