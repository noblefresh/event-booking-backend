<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Common query scopes shared across models for filtering and searching.
 */
trait CommonQueryScopes
{
    /**
     * Scope a query to filter by a date range on start_date/end_date columns.
     */
    public function scopeFilterByDate(Builder $query, ?string $from = null, ?string $to = null): Builder
    {
        return $query
            ->when($from, fn (Builder $q) => $q->whereDate('start_date', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('end_date', '<=', $to));
    }

    /**
     * Scope a query to search records by a title column.
     */
    public function scopeSearchByTitle(Builder $query, ?string $term = null): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where('title', 'like', '%'.$term.'%'));
    }
}


