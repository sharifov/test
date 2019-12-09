<?php

namespace sales\yii\grid;

use Yii;
use sales\access\ListsAccess;
use yii\grid\DataColumn;

class UserColumn extends DataColumn
{
    public $format = 'userName';

    public $userId;

    public function init(): void
    {
        parent::init();
        if (!$this->filter) {
            if (!$this->userId) {
                $this->userId = Yii::$app->user->id ?? null;
            }
            $this->filter = (new ListsAccess($this->userId))->getEmployees();
        }
    }
}
