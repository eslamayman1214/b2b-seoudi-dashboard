<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVersionsTable extends Migration
{
    public function up()
    {
        Schema::create('product_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('sku');
            $table->string('item_code');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->json('tiers'); // Store tiers as JSON
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_versions');
    }
}
