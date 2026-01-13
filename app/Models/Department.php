<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    public function categories() : HasMany
    {
        return $this->hasMany(Category::class);
    }
}
