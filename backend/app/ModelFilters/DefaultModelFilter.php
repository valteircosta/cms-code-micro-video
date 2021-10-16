<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

abstract class DefaultModelFilter extends ModelFilter
{

    protected $sortable = [];

    public function setup()
    {
        // Method that can not to be called
        $this->blacklistMethod('isSortable');

        // url-name?sort=created_at?dir=desc
        $noSort = ($this->input('sort', '') === '');
        if ($noSort) {
            $this->orderBy('created_at', 'DESC');
        }
    }
    public function sort($column)
    {
        // Called customized method sortBy
        if (\method_exists($this, $method = 'sortBy' . \Str::studly($column))) {
            $this->$method();
        }

        if ($this->isSortable($column)) {
            $dir = \Str::lower($this->input('dir')) == 'asc' ? 'ASC' : 'DESC';
            $this->orderBy($column, $dir);
        }
    }
    protected function isSortable($column)
    {
        return \in_array($column, $this->sortable);
    }
}
