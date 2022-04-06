<?php

namespace src\model\emailQuote\entity;

/**
 * This is the ActiveQuery class for [[EmailQuote]].
 *
 * @see EmailQuote
 */
class EmailQuoteQuery
{
    /**
     * @param int $id
     * @return EmailQuote[]
     */
    public static function getGroupedByEmailId(int $id): array
    {
        return EmailQuote::find()->byEmailId($id)->groupBy(['eq_email_id', 'eq_quote_id'])->all();
    }
}
