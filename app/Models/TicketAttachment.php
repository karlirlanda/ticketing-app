<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'ticket_attachments';

    protected $connection = 'mysql';

    protected $fillable = [
        'ticket_id',
        'file',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
