<?php

namespace sales\services\cleaner;

/**
 * Class BaseCleaner
 */
class BaseCleaner
{
    private string $table;
    private string $column;

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function setColumn(string $column): void
    {
        $this->column = $column;
    }
}
