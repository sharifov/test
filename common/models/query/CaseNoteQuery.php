<?php

namespace common\models\query;

use common\models\CaseNote;

/**
 * This is the ActiveQuery class for [[CaseNote]].
 *
 * @see CaseNote
 */
class CaseNoteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CaseNote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CaseNote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
