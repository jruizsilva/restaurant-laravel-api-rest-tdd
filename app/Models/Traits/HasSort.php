<?php

namespace App\Models\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasSort
{
    private function sortFields()
    {
        return ["id"];
    }

    public function scopeSort(Builder $builder, $sortBy = "id", $sortDirection = "desc"): Builder
    {
        if (!in_array($sortBy, $this->sortFields())) {
            abort(400, "Invalid sort field");
        }
        if (!in_array($sortDirection, ["asc", "desc"])) {
            $sortDirection = "desc";
        }
        return $builder->orderBy($sortBy, $sortDirection);
    }
}