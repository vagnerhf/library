<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'birth_date', 'key'];
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->key = Str::uuid()->toString();
        });
    }
    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
