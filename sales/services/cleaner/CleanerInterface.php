<?php


namespace sales\services\cleaner;

use sales\services\cleaner\form\DbCleanerParamsForm;

/**
 * Interface CleanerInterface
 * @property string $table
 * @property string $column
 */
interface CleanerInterface
{
    public function runDeleteByForm(DbCleanerParamsForm $form): int;

    public function getTable(): string;

    public function getColumn(): string;

    public function setTable(string $table): void;

    public function setColumn(string $column): void;
}
