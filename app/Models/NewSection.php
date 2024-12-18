<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSection extends Model
{
    use HasFactory;

    protected $table = 'new_sections';

    protected $fillable = [
        'section',
        'view_name',
        'description',
    ];
}
