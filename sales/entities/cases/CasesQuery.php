<?php

namespace sales\entities\cases;

/**
 * This is the ActiveQuery class for [[Cases]].
 *
 * @see Cases
 */
class CasesQuery extends \yii\db\ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return Cases[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Cases|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
