<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

    protected $primaryKey = 'id';

    protected $fillable = [

        'user_id',
        'amount',
        'description',
        'status',
        'deb',
        'due_date',
        'type_id',
        'installments',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
