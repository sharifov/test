<?php

namespace sales\yii\grid;

use common\models\Employee;
use Yii;
use sales\access\ListsAccess;
use yii\grid\DataColumn;

/**
 * Class UserColumn
 *
 * @property $userId
 * @property $relation
 */
class UserColumn extends DataColumn
{
    public $label = 'Employee';

    public $format = 'userName';

    public $userId;

    public $relation;

    public function init(): void
    {
        parent::init();

        if (empty($this->relation)) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if ($this->filter === null) {
            if (!$this->userId) {
                $this->userId = Yii::$app->user->id ?? null;
            }
            $this->filter = (new ListsAccess($this->userId))->getEmployees();
        }
    }

    public function getDataCellValue($model, $key, $index)
    {
        if ($model->{$this->attribute} && ($user = $model->{$this->relation})) {
            /** @var Employee $user */
            return $user->username;
        }

        return null;
    }
}
