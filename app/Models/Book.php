<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'publication_year', 'author_id', 'key'];
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'publication_year' => 'integer',
        'author_id' => 'integer',
    ];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->key = Str::uuid()->toString();
        });
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
