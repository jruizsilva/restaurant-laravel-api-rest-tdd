<?php

namespace App\Models\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasSearch
{
    private function searchFields()
    {
        return [];
    }

    public function scopeSearch(Builder $builder, string $search = "")
    {
        if ($search === "") {
            return $builder;
        }
        $fields = $this->searchFields();
        $builder->where(function (Builder $builder) use ($fields, $search) {
            $wordsToSearch = explode(" ", $search);
            foreach ($wordsToSearch as $word) {
                $builder->whereAny($fields, "like", "%$word%");
            }
        });
    }
}