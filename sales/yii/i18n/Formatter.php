<?php

namespace sales\yii\i18n;

use common\models\Employee;
use common\models\Quote;
use yii\bootstrap4\Html;

class Formatter extends \yii\i18n\Formatter
{
    public function asQuoteType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        switch ($value) {
            case Quote::TYPE_BASE:
                $class = 'label label-info';
                break;
            case Quote::TYPE_ORIGINAL:
                $class = 'label label-success';
                break;
            case Quote::TYPE_ALTERNATIVE:
                $class = 'label label-warning';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', Quote::getTypeName($value), [
            'class' => $class,
        ]);
    }

    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asByUserDateTime($dateTime): string
    {
        return $dateTime ? '<i class="fa fa-calendar"></i> ' . $this->asDatetime(strtotime($dateTime), 'php:d-M-Y [H:i]') : $this->nullDisplay;
    }

    /**
     * @param Employee|int|string|null $user
     * @return string
     */
    public function asUserName($user): string
    {
        if (!$user) {
            return $this->nullDisplay;
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
