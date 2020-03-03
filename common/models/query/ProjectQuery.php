<?php

namespace common\models\query;

use common\models\Project;
use yii\db\ActiveQuery;

/**
 * Class ProjectQuery
 *
 * @see Project
 */
class ProjectQuery extends ActiveQuery
{
    public function active(): self
    {
        return $this->andWhere(['closed' => false]);
    }
}
