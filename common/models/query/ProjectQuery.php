<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class ProjectQuery
 */
class ProjectQuery extends ActiveQuery
{
    /**
     * @return ProjectQuery
     */
    public function active(): ProjectQuery
    {
        return $this->andWhere(['closed' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
