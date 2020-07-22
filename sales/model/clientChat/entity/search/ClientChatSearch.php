<?php

namespace sales\model\clientChat\entity\search;

use common\models\Employee;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\ClientChat;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;

/**
 * Class ClientChatSearch
 *
 * @property int|null $lead_id
 * @property int|null $case_id
 */
class ClientChatSearch extends ClientChat
{
    public $lead_id;
    public $case_id;

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

            ['cch_note', 'safe'],

            ['cch_owner_user_id', 'integer'],

            ['cch_project_id', 'integer'],

            ['cch_rid', 'safe'],

            ['cch_status_id', 'integer'],

            ['cch_title', 'safe'],

            ['cch_ua', 'integer'],

            ['cch_updated_dt', 'safe'],

            ['cch_updated_user_id', 'integer'],

            ['lead_id', 'integer'],

            ['case_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $query->joinWith(['leads', 'cases']);

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
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'date_format(cch_created_dt, "%Y-%m-%d")' => $this->cch_created_dt,
            'date_format(cch_updated_dt, "%Y-%m-%d")' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'ccl_lead_id' => $this->lead_id,
            'cccs_case_id' => $this->case_id,
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
}
