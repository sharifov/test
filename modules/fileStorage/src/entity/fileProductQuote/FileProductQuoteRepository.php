<?php

namespace modules\fileStorage\src\entity\fileProductQuote;

class FileProductQuoteRepository
{
    public function save(FileProductQuote $model): void
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('FileProductQuote saving error.');
        }
    }

    public function remove(FileProductQuote $model): void
    {
        if (!$model->delete()) {
            throw new \RuntimeException('FileProductQuote removing error.');
        }
    }
}
