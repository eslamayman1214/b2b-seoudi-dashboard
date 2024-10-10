<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // Unique ID for each ticket
            $table->string('description'); // Problem description
            $table->string('section'); // Section associated with the ticket
            $table->string('email'); // Email of the user reporting the issue
            $table->string('attachment')->nullable(); // Attachment path (optional)
            $table->enum('status', ['pending', 'in progress', 'resolved', 're-open'])->default('pending'); // Status of the ticket
            $table->string('assigned')->nullable(); // Name of the assignee, if assigned
            $table->timestamps(); // Automatically includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
