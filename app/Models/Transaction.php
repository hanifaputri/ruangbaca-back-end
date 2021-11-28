<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'book_id', 'user_id', 'deadline'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get book associated with transaction
     * 
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get user associated with transaction
     * 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
