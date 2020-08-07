<?php

namespace App\Filter\Capture;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

class CapturedAtFilter extends Filter
{
    /**
     * Apply the captured_at condition to the query.
     *
     * @param Builder $builder
     * @param mixed   $value
     *
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->whereDate('captured_at', '=', $value);
    }
}
