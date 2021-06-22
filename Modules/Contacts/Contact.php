<?php

namespace Modules\Contacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Contact extends Model
{
    use Notifiable;

    protected $table = 'contacts';
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sender' , 'subject', 'message' , 'is_read'
    ];

    public function scopeRead($query)
    {
        return $query->where('is_read', 'Y');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', 'N');
    }
}
