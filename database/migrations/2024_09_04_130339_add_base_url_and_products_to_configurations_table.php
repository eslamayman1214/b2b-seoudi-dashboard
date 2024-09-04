<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBaseUrlAndProductsToConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('base_url')->nullable();
            $table->string('products_endpoint')->nullable();
            $table->string('products_token')->nullable();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['base_url', 'products_endpoint', 'products_token']);
        });
    }
}
