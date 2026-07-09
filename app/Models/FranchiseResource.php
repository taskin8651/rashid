<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseResource extends Model
{
    protected $fillable = ['title', 'description', 'file_path', 'original_name'];
}
