<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerEndpointAndTokenToConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('customer_endpoint')->nullable();
            $table->string('customer_token')->nullable();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn(['customer_endpoint', 'customer_token']);
        });
    }
}
