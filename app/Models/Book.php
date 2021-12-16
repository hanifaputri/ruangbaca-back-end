<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // TODO: Insert your fillable fields
        'isbn', 'title', 'img_url', 'publisher_id', 'category_id', 'language_id', 'status', 'author' 
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // TODO: Insert your hidden fields
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Define relationship betweeen books and transactions
     * 
     * 
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
