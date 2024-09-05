<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiersTable extends Migration
{
    public function up()
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('tier_name')->default('tier1');
            $table->integer('min_quantity')->nullable();
            $table->integer('max_quantity')->nullable();
            $table->decimal('value', 8, 2)->nullable();
            $table->enum('type', ['price', 'percentage'])->default('price');
            $table->string('customer_group');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tiers');
    }
}