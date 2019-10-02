<?php

namespace sales\helpers\user;

use sales\repositories\NotFoundException;
use Yii;
use common\models\Employee;

class UserFinder
{

    /**
     * Search only active user
     *
     * @param int|null $userId
     * @return Employee
     */
    public static function find(?int $userId = null): Employee
    {
        /** @var Employee $user */
        if ($userId) {
            if (isset(Yii::$app->user->identity) && Yii::$app->user->identity instanceof Employee && Yii::$app->user->identity->getId() === $userId) {
                $user = Yii::$app->user->identity;
            } elseif ((!$user = Employee::findOne($userId)) || !$user->isActive()) {
                throw new NotFoundException('User not found or user is inactive');
            }
            return $user;
        }
        if (isset(Yii::$app->user->identity) && Yii::$app->user->identity instanceof Employee) {
            $user = Yii::$app->user->identity;
            return $user;
        }
        throw new NotFoundException('User not found');
    }

    /**
     * @return int|null
     */
    public static function getCurrentUserId(): ?int
    {
        try {
            $createdUserId = self::find()->id;
        } catch (NotFoundException $e) {
            $createdUserId = null;
        }
        return $createdUserId;
    }

}
