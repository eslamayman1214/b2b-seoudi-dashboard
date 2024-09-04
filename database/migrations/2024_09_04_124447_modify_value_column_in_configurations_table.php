<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyValueColumnInConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('value')->change();
        });
    }

    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->integer('value')->change(); // Or revert to the original type
        });
    }
}
