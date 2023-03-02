<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manuscript extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getMeta(string $key): string
    {
        return 'ToDo:'.$key;
    }
}
