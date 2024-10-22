<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_name',
        'ticket_created_date',
        'assigned_date',
        'pending_date',
        'in_progress_date',
        'resolved_date',
        'sla_status',
        'sla_duration',
    ];

    // Define relationship with the Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Define relationship with the User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
