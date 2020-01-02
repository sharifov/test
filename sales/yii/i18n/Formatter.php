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

        return Html::tag('span', Quote::getTypeName($value), ['class' => $class]);
    }

    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asByUserDateTime($dateTime): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $this->asDatetime(strtotime($dateTime), 'php:d-M-Y [H:i]');
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
        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($userName);
    }

    /**
     * @param $value
     * @return string
     */
    public function asBooleanByLabel($value): string
    {
        if ($value) {
            return Html::tag('span', 'Yes', ['class' => 'label label-success']);
        }
        return Html::tag('span', 'No', ['class' => 'label label-danger']);
    }
}
