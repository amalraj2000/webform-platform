<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormVersion extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'version_number', 'schema', 'is_published'];

    protected $casts = [
        'schema' => 'array',
        'is_published' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
