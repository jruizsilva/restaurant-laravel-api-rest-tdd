<?php

namespace App\Models;

use App\Models\Traits\HasSearch;
use App\Models\Traits\HasSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory, HasSearch, HasSort;

    protected $guarded = [];

    private function searchFields()
    {
        return [
            'name',
            'description',
        ];
    }
    private function sortFields()
    {
        return [
            'id',
            'name',
            'description',
            'created_at',
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