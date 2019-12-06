<?php

namespace frontend\extensions\grid;

use Yii;
use sales\access\ListsAccess;
use yii\grid\DataColumn;

class UsersColumn extends DataColumn
{
    public $format = 'userName';

    public function init(): void
    {
        parent::init();
        $this->filter = (new ListsAccess(Yii::$app->user->id))->getEmployees();
    }
}
