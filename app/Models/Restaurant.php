<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = [];

    protected function searchFields()
    {
        return [
            'name',
            'description',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plates()
    {
        return $this->hasMany(Plate::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}