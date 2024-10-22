<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketPerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_performances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Assignee’s name to avoid frequent joins
            $table->timestamp('ticket_created_date'); // Date when the ticket was created
            $table->timestamp('assigned_date')->nullable(); // Date when assigned to the user
            $table->timestamp('pending_date')->nullable(); // Date when marked as pending
            $table->timestamp('in_progress_date')->nullable(); // Date when marked as in-progress
            $table->timestamp('resolved_date')->nullable(); // Date when resolved
            $table->enum('sla_status', ['in_sla', 'out_sla'])->nullable(); // To indicate SLA status
            $table->integer('sla_duration')->nullable(); // Duration between assigned and resolved in minutes

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_performances');
    }
}