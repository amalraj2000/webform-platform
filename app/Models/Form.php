<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'account_id',
        'title',
        'description',
        'published_version_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function versions()
    {
        return $this->hasMany(FormVersion::class);
    }

    public function publishedVersion()
    {
        return $this->belongsTo(FormVersion::class, 'published_version_id');
    }

    public function submissions()
    {
        return $this->hasManyThrough(Submission::class, FormVersion::class);
    }
}
