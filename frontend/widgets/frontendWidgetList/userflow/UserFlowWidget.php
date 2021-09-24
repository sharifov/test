<?php

namespace frontend\widgets\frontendWidgetList\userflow;

use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\base\InvalidConfigException;
use frontend\widgets\frontendWidgetList\userflow\abac\dto\UserFlowWidgetDto;
use frontend\widgets\frontendWidgetList\userflow\abac\UserFlowWidgetObject;

/**
 * LouAssistWidget
 *
 * @property array|null $params
 */
class UserFlowWidget extends \yii\bootstrap\Widget
{
    public $params;

    public function beforeRun()
    {
        if (!parent::beforeRun()) {
            return false;
        }

        /** @abac null, UserFlowWidgetObject::UI_INCLUDE_WIDGET, UserFlowWidgetObject::ACTION_INCLUDE, Run UserFlowWidget */
        if (\Yii::$app->abac->can(null, UserFlowWidgetObject::UI_INCLUDE_WIDGET, UserFlowWidgetObject::ACTION_INCLUDE)) {
            return true;
        }

        return false; // false to not run the widget
    }

    public function run()
    {
        if (!$token = ArrayHelper::remove($this->params, 'token')) {
            throw new \RuntimeException('"token" is required in UserFlowWidget params');
        }

        return $this->render('view', [
            'token' => $token,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->full_name,
            'user_email' => Auth::user()->email,
            'user_signed_up_datetime' => date(DATE_ISO8601, strtotime(Auth::user()->created_at)),
        ]);
    }
}
