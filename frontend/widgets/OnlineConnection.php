<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Lead;
use sales\entities\cases\Cases;
use sales\helpers\UserCallIdentity;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use Yii;

/**
 * OnlineConnection widget
 *
 * @author Alexandr <alex.connor@techork.com>
 */
class OnlineConnection extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $leadId = null;
        $caseId = null;
        $subList = [];
        $userId = Yii::$app->user->id;

        if (Yii::$app->controller->action->uniqueId === 'lead/view') {
            $leadId = Yii::$app->request->get('id');
            if (!$leadId) {
                $gid = Yii::$app->request->get('gid');
                if ($gid) {
                    $lead = Lead::find()->select(['id'])->where(['gid' => $gid])->asArray()->one();
                    if ($lead && $lead['id']) {
                        $leadId = $lead['id'];
                        $subList[] = 'lead-' . $leadId;
                        unset($lead);
                    }
                }
            }
        }

        if (Yii::$app->controller->action->uniqueId === 'cases/view') {
            $gid = Yii::$app->request->get('gid');
            if ($gid) {
                $case = Cases::find()->select(['cs_id'])->where(['cs_gid' => $gid])->limit(1)->asArray()->one();
                if ($case && $case['cs_id']) {
                    $caseId = $case['cs_id'];
                    $subList[] = 'case-' . $caseId;
                    unset($case);
                }
            }
        }

        if (Yii::$app->controller->action->uniqueId === 'client-chat/index') {
            $cchId = Yii::$app->request->get('chid');
            if ($cchId) {
                $chat = ClientChat::find()->select(['cch_id'])->byId($cchId)->asArray()->one();
                if ($chat && $chat['cch_id']) {
                    $subList[] = 'chat-' . $chat['cch_id'];
                    unset($chat);
                }
            }
            if ($channels = ClientChatChannel::getListByUserId($userId)) {
                foreach ($channels as $chanelId => $chanelName) {
                    $subList[] = 'channel-' . $chanelId;
                }
            }
        }

        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        $pageUrl = urlencode(\yii\helpers\Url::current());
        $ipAddress = Yii::$app->request->remoteIP;
        $webSocketHost = (Yii::$app->request->isSecureConnection ? 'wss': 'ws') . '://'.Yii::$app->request->serverName . '/ws';// . ':8888';

        return $this->render('online_connection2', [
            'userId' =>  $userId,
            'userIdentity' =>  UserCallIdentity::getClientId($userId),
            'controllerId' => $controllerId,
            'actionId' => $actionId,
            'pageUrl' => $pageUrl,
            'ipAddress' =>  $ipAddress,
            'webSocketHost' => $webSocketHost,
            'subList' => $subList,
            'leadId' => $leadId,
            'caseId' => $caseId
        ]);
    }
}
