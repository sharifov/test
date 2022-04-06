<?php

namespace src\model\emailQuote;

use src\model\emailQuote\entity\EmailQuote;

class EmailQuoteRepository
{
    public function save(EmailQuote $emailQuote): int
    {
        if (!$emailQuote->save()) {
            throw new \RuntimeException($emailQuote->getErrorSummary(true)[0]);
        }
        return $emailQuote->eq_id;
    }
}
