<?php

namespace sales\model\clientChat\entity\search;

use common\models\Employee;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\ClientChat;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Query;

class ClientChatSearch extends ClientChat
{
    public string $timeRange;
    public string $timeStart;
    public string $timeEnd;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->timeRange = date('Y-m-d 00:00:00', strtotime('-29 days')) . ' - ' . date('Y-m-d 23:59:59');
        $range = explode(' - ', $this->timeRange);
        $this->timeStart = $range[0];
        $this->timeEnd = $range[1];
    }

    public function rules(): array
    {
        return [
            [['timeRange', 'timeStart', 'timeEnd'], 'string' ],
            ['cch_case_id', 'integer'],

            ['cch_ccr_id', 'integer'],

            ['cch_channel_id', 'integer'],

            ['cch_client_id', 'integer'],

            ['cch_created_dt', 'safe'],

            ['cch_created_user_id', 'integer'],

            ['cch_dep_id', 'integer'],

            ['cch_description', 'safe'],

            ['cch_id', 'integer'],

            ['cch_ip', 'safe'],

            ['cch_language_id', 'safe'],

            ['cch_lead_id', 'integer'],

            ['cch_note', 'safe'],

            ['cch_owner_user_id', 'integer'],

            ['cch_project_id', 'integer'],

            ['cch_rid', 'safe'],

            ['cch_status_id', 'integer'],

            ['cch_title', 'safe'],

            ['cch_ua', 'integer'],

            ['cch_updated_dt', 'safe'],

            ['cch_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cch_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_case_id' => $this->cch_case_id,
            'cch_lead_id' => $this->cch_lead_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'date_format(cch_created_dt, "%Y-%m-%d")' => $this->cch_created_dt,
            'date_format(cch_updated_dt, "%Y-%m-%d")' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        return $dataProvider;
    }

    public function report($params)
    {
        $this->load($params);
        if ($this->timeRange) {
            $range = explode(' - ', $this->timeRange);
            $this->timeStart = $range[0];
            $this->timeEnd = $range[1];
        }

        $query = static::find()->joinWith('cchOwnerUser');
        $query->select([
            'username',
            'cch_owner_user_id AS owner',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_GENERATED .', 1, 0)) AS generated',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_CLOSED .', 1, 0)) AS closed',
        ]);

        $query->where('cch_owner_user_id IS NOT NULL');
        $query->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd))
        ]);

        $query->groupBy(['owner']);

        $queryMessages = ClientChatMessage::find();
        $queryMessages->select([
            'ccm_user_id AS user',
            'SUM(CASE WHEN ccm_user_id IS NOT NULL THEN 1 ELSE 0 END) AS messages',
        ]);

        $queryMessages->andWhere([
            'between',
            'ccm_sent_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd))
        ]);

        $queryMessages->groupBy(['user']);

        $clientChat = $query->createCommand()->queryAll();
        $clientChatMsg = $queryMessages->createCommand()->queryAll();

        foreach ($clientChat as $key => $item)
        {
            $clientChat[$key]['msg'] = '';
            foreach ($clientChatMsg as $msg)
            {
                if ($item['owner'] == $msg['user']){
                    $clientChat[$key]['msg'] = (string)$msg['messages'];
                }
            }
        }

        $paramsData = [
            'allModels' => $clientChat,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'username',
                    'generated',
                    'closed',
                    'msg',
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return new ArrayDataProvider($paramsData);
    }

    public function searchRealtimeClientChatActivity($params)
    {
        $queryChats = static::find()->joinWith(['cchOwnerUser', 'cchClient as c', 'cchProject as p', 'cchDep as d', 'cchChannel as ch']);
        $queryChats->select([
            'cch_id',
            'cch_client_id',
            'cch_created_dt',
            'cch_owner_user_id',
            'username',
            'CONCAT(c.first_name, " ", c.last_name ) as clientName',
            'p.name as project',
            'd.dep_name as department',
            'ch.ccc_name as channel'
            ]);
        $queryChats->where(['cch_status_id' => ClientChat::STATUS_GENERATED]);
        if($params['formDate']){
            $queryChats->where(['between','cch_created_dt', $params['formDate'], date('Y-m-d H:i:s')]);
        } else {
            $queryChats->limit(10);
        }
        $queryChats->orderBy('cch_created_dt ASC');
        $chatCmd = $queryChats->createCommand();
        $clientChats = $chatCmd->queryAll();

        $queryMessages = ClientChatMessage::find();
        $queryMessages->select([
            'ccm_cch_id AS chatId',
            'SUM(CASE WHEN ccm_user_id IS NOT NULL THEN 1 ELSE 0 END) AS outMsg',
            'SUM(CASE WHEN ccm_user_id IS NULL THEN 1 ELSE 0 END) AS inMsg',
        ]);

        $queryMessages->groupBy('ccm_cch_id');
        $msgCmd = $queryMessages->createCommand();
        $chatMessages = $msgCmd->queryAll();

        //var_dump($msgCmd); die();

        foreach ($clientChats as $key => $chat){
            $clientChats[$key]['outMsg'] = 0;
            $clientChats[$key]['inMsg'] = 0;
            foreach ($chatMessages as $message){
                if ($chat['cch_id'] == $message['chatId'])
                {
                    $clientChats[$key]['outMsg'] = $message['outMsg'];
                    $clientChats[$key]['inMsg'] = $message['inMsg'];
                }
            }
        }

        return $clientChats;
    }
}
