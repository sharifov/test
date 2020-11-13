<?php

namespace sales\model\user\entity\monitor\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\entity\monitor\UserMonitor;

/**
 * UserMonitorSearch represents the model behind the search form of `sales\model\user\entity\monitor\UserMonitor`.
 */
class UserMonitorSearch extends UserMonitor
{
    public string $timeRange = '';
    public string $startTime = '';
    public string $endTime = '';

    public $userId;
    public $typeId;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['um_id', 'um_user_id', 'um_type_id', 'um_period_sec', 'userId', 'typeId'], 'integer'],
            [['um_description', 'timeRange', 'startTime', 'endTime'], 'safe'],
            [['um_start_dt', 'um_end_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return [
            'um_id' => 'ID',
            'userId' => 'User ID',
            'typeId' => 'Type ID'
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserMonitor::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->userId){
            $query->andWhere(['um_user_id' => $this->userId]);
        }

        if ($this->typeId){
            $query->andWhere(['um_type_id' => $this->typeId]);
        }

        if (!empty($this->startTime) && !empty($this->endTime)){
            $query->andWhere(['>=', 'um_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->startTime))]);
            $query->andWhere(['<=', 'um_end_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->endTime))]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'um_id' => $this->um_id,
            'um_user_id' => $this->um_user_id,
            'um_type_id' => $this->um_type_id,
            'um_start_dt' => $this->um_start_dt,
            'um_end_dt' => $this->um_end_dt,
            'um_period_sec' => $this->um_period_sec,
        ]);

        $query->andFilterWhere(['like', 'um_description', $this->um_description]);

        return $dataProvider;
    }


    /**
     * @param $params
     * @param string $startDateTime
     * @return mixed
     */
    public function searchStats($params, string $startDateTime = '')
    {
        $query = UserMonitor::find()->with('umUser');
        $users = [];
        $user2row = [];
        $data['users'] = $users;
        $data['items'] = [];
        $data['user2row'] = $user2row;


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $data;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'um_id' => $this->um_id,
            'um_user_id' => $this->um_user_id,
            'um_type_id' => $this->um_type_id,
            'um_start_dt' => $this->um_start_dt,
            'um_end_dt' => $this->um_end_dt,
            'um_period_sec' => $this->um_period_sec,
        ]);

        if ($startDateTime) {
            $query->andWhere(['>=', 'um_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($startDateTime))]);
        }

        //$query->andFilterWhere(['like', 'um_description', $this->um_description]);

        /** @var UserMonitor[] $items */
        $items = $query->all();

        if ($items) {
            $n = 1;
            foreach ($items as $item) {
                $users[$item->um_user_id] = $item->umUser->username;
                if (isset($user2row[$item->um_user_id])) {
                    continue;
                }
                $user2row[$item->um_user_id] = $n++;
            }
        }
        $data['items'] = $items;
        $data['users'] = $users;
        $data['user2row'] = $user2row;

        return $data;
    }
}
