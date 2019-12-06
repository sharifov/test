<?php

namespace sales\formatters;

use common\models\Employee;
use yii\bootstrap4\Html;
use yii\i18n\Formatter;
use Yii;

class DetailViewFormatter extends Formatter
{
    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asDateByUserDt($dateTime): string
    {
        $this->datetimeFormat = 'php:d-M-Y [H:i]';
        return $dateTime ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($dateTime)) : '-';
    }

    /**
     * @param $userId
     * @return string
     */
    public function asUserName($userId): string
    {
        if (!$userId) {
            return '';
        }
        if ($user = Employee::findOne($userId)) {
            return '<i class="fa fa-user"></i> ' . Html::encode($user->username);
        }
        return 'User not found';
    }

    /**
     * @param $value
     * @return string
     */
    public function asBooleanByLabel($value): string
    {
        if ($value) {
            return '<span class="label label-success">Yes</span>';
        }
        return '<span class="label label-danger">No</span>';
    }
}
