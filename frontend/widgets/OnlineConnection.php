<?php

namespace frontend\widgets;

use common\models\Lead;
use sales\entities\cases\Cases;
use sales\helpers\UserCallIdentity;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * OnlineConnection widget
 *
 * @author Alexandr <alex.connor@techork.com>
 */
class OnlineConnection extends \yii\bootstrap\Widget
{
    public const CHAT_SUBSCRIBE_LIST = [
        'client-chat/index',
        'client-chat/view',
        'client-chat/dashboard-v2',
    ];

    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $leadId = null;
        $caseId = null;
        $subList = [];
        $userId = Yii::$app->user->id;

        $this->subscribeToLeadChannel($leadId, $subList);
        $this->subscribeToCaseChannel($caseId, $subList);
        $this->subscribeToClientChatChannel($userId, $subList);
        $pageUrl = urlencode(\yii\helpers\Url::current());
        $wsHost = (Yii::$app->request->isSecureConnection ? 'wss' : 'ws') .
            '://' . Yii::$app->request->serverName . '/ws';

        $urlParams = [
            'user_id' => $userId,
            'controller_id' => Yii::$app->controller->id,
            'action_id' => Yii::$app->controller->action->id,
            'page_url' => $pageUrl,
            'lead_id' => $leadId,
            'case_id' => $caseId,
            'ip'    => Yii::$app->request->remoteIP,
            'sub_list' => $subList,
        ];

        $wsUrl = $wsHost . '/?' . http_build_query($urlParams);

        return $this->render('online_connection', [
            'userId' =>  $userId,
            'wsUrl' =>  $wsUrl,
            'userIdentity' =>  UserCallIdentity::getClientId($userId)
        ]);
    }

    /**
     * @param $leadId
     * @param $subList
     */
    private function subscribeToLeadChannel(&$leadId, &$subList): void
    {
        if (Yii::$app->controller->action->uniqueId === 'lead/view') {
            $leadId = Yii::$app->request->get('id');
            if (!$leadId) {
                $gid = Yii::$app->request->get('gid');
                if ($gid) {
                    $lead = Lead::find()->select(['id'])->where(['gid' => $gid])->asArray()->one();
                    if ($lead && !empty($lead['id'])) {
                        $leadId = $lead['id'];
                        $subList[] = 'lead-' . $leadId;
                        unset($lead);
                    }
                }
            }
        }
    }

    /**
     * @param $caseId
     * @param $subList
     */
    private function subscribeToCaseChannel(&$caseId, &$subList): void
    {
        if (Yii::$app->controller->action->uniqueId === 'cases/view') {
            $gid = Yii::$app->request->get('gid');
            if ($gid) {
                $case = Cases::find()->select(['cs_id'])->where(['cs_gid' => $gid])->limit(1)->asArray()->one();
                if ($case && !empty($case['cs_id'])) {
                    $caseId = $case['cs_id'];
                    $subList[] = 'case-' . $caseId;
                    unset($case);
                }
            }
        }
    }

    /**
     * @param $userId
     * @param $subList
     */
    private function subscribeToClientChatChannel($userId, &$subList): void
    {
        if (ArrayHelper::isIn(Yii::$app->controller->action->uniqueId, self::CHAT_SUBSCRIBE_LIST)) {
            $cchId = Yii::$app->request->get('chid');
            if ($cchId) {
                $chat = ClientChat::find()->select(['cch_id'])->byId($cchId)->asArray()->one();
                if ($chat && !empty($chat['cch_id'])) {
                    $subList[] = 'chat-' . $chat['cch_id'];
                    unset($chat);
                }
            }
            if ($channels = ClientChatChannel::getListByUserId($userId)) {
                foreach ($channels as $channelId => $channelName) {
                    $subList[] = ClientChatChannel::getPubSubKey($channelId);
                }
            }
        }
    }
}
