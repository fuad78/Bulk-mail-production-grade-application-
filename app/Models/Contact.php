<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['contact_list_id', 'email', 'name', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function contactList()
    {
        return $this->belongsTo(ContactList::class);
    }
}
