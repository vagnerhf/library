<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'user_id', 'loan_date', 'return_date', 'key'];
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->key = Str::uuid()->toString();
        });
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
