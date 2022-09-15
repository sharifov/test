<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\QuoteSearchCid]].
 *
 * @see \common\models\QuoteSearchCid
 */
class QuoteSearchCidScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return \common\models\QuoteSearchCid[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\QuoteSearchCid|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
