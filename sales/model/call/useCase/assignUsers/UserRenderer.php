<?php

namespace sales\model\call\useCase\assignUsers;

use common\models\Employee;
use yii\helpers\Html;

class UserRenderer
{
    public static function render(Employee $user): string
    {
        $isCallFree = $user->isCallFree();
        $isCallStatusReady = $user->isCallStatusReady();

        if ($isCallFree && $isCallStatusReady) {
            $class = 'text-success';
        } elseif ($isCallStatusReady) {
            $class = 'text-warning';
        } else {
            $class = 'text-danger';
        }

        if ($user->isAdmin()) {
            $iconClass = 'fa-android';
        } elseif ($user->isSupervision() || $user->isSupSuper() || $user->isExSuper()) {
            $iconClass = 'fa-user-md';
        } elseif ($user->isQa()) {
            $iconClass = 'fa-linux';
        } else {
            $iconClass = 'fa-user';
        }

        $out = Html::tag('i', '', ['class' => 'fa ' . $iconClass . ' fa-lg ' . $class, 'title' => $user->id]);
        $out .= ' ' . Html::encode($user->username);
        return $out;
    }
}
