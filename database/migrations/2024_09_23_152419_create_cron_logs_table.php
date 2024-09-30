<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronLogsTable extends Migration
{
    public function up()
    {
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('executed_at')->nullable();
            $table->integer('edited_products_count')->default(0);
            $table->string('status'); // e.g., Success or Failure
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cron_logs');
    }
}
