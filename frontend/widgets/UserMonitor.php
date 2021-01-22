<?php

/**
 * @author Alexandr <alex.connor@techork.com>
 */

namespace frontend\widgets;

use common\models\UserConnection;
use Yii;

/**
 * UserMonitor widget
 *
 * @property bool $isIdleMonitorEnabled
 * @property bool $isAutoLogoutEnabled
 * @property array $excludedActions
 */
class UserMonitor extends \yii\bootstrap\Widget
{
    public bool $isIdleMonitorEnabled = true;
    public bool $isAutoLogoutEnabled = true;

    private array $excludedActions  = [
        'call/realtime-map'
    ];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (in_array(Yii::$app->controller->action->uniqueId, $this->excludedActions, true)) {
            return '';
        }

        $this->isAutoLogoutEnabled = \sales\model\user\entity\monitor\UserMonitor::isAutologoutEnabled();
        $this->isIdleMonitorEnabled = UserConnection::isIdleMonitorEnabled();

        $userId = Yii::$app->user->id;
        return $this->render('user_monitor', [
            'userId' =>  $userId,
            'isAutoLogoutEnabled' => $this->isAutoLogoutEnabled,
            'isIdleMonitorEnabled' => $this->isIdleMonitorEnabled,
        ]);
    }
}