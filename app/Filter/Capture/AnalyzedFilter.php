<?php

namespace App\Filter\Capture;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

class AnalyzedFilter extends Filter
{
    /**
     * Apply the analyzed condition to the query.
     *
     * @param Builder $builder
     * @param mixed   $value
     *
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->where('analyzed', '=', $value);
    }
}
