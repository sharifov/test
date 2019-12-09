<?php

namespace sales\yii\i18n;

use common\models\Employee;
use yii\bootstrap4\Html;
use Yii;

class Formatter extends \yii\i18n\Formatter
{
    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asDateTimeByUserDt($dateTime): string
    {
        $this->datetimeFormat = 'php:d-M-Y [H:i]';
        return $dateTime ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($dateTime)) : '-';
    }

    /**
     * @param Employee|int|string|null $user
     * @return string
     */
    public function asUserName($user): string
    {
        if (!$user) {
            return '';
        }
        if ($user instanceof Employee) {
            $userName = $user->username;
        } elseif (is_int($user)) {
            if ($findUser = Employee::findOne($user)) {
                $userName = $findUser->username;
            } else {
                return 'not found';
            }
        } elseif (is_string($user)) {
            $userName = $user;
        } else {
            throw new \InvalidArgumentException('user must be Employee|int|string|null');
        }
        return '<i class="fa fa-user"></i> ' . Html::encode($userName);
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
