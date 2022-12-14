<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadTask;

/**
 * LeadTaskSearch represents the model behind the search form of `common\models\LeadTask`.
 */
class LeadTaskSearch extends LeadTask
{
    public $status_not_in;
    public $status;

    public $datetime_start;
    public $datetime_end;
    public $date_range;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['lt_lead_id', 'lt_task_id', 'lt_user_id'], 'integer'],
            [['lt_notes', 'status_not_in', 'status'], 'safe'],
            [['lt_date', 'lt_completed_dt', 'lt_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LeadTask::find()->with('ltUser', 'ltLead', 'ltTask');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lt_date' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (empty($this->lt_date) && isset($params['LeadTaskSearch']['date_range'])) {
            $query->andFilterWhere(['>=', 'DATE(lt_date)', $this->datetime_start])
                ->andFilterWhere(['<=', 'DATE(lt_date)', $this->datetime_end]);
        }

        if (isset($params['LeadTaskSearch']['lt_date'])) {
            $query->andFilterWhere(['=','DATE(lt_date)', $this->lt_date]);
        }

        if ($this->lt_completed_dt) {
            $query->andFilterWhere(['>=', 'lt_completed_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lt_completed_dt))])
                ->andFilterWhere(['<=', 'lt_completed_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lt_completed_dt) + 3600 * 24)]);
        }

        if ($this->lt_updated_dt) {
            $query->andFilterWhere(['>=', 'lt_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lt_updated_dt))])
                ->andFilterWhere(['<=', 'lt_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lt_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lt_lead_id' => $this->lt_lead_id,
            'lt_task_id' => $this->lt_task_id,
            'lt_user_id' => $this->lt_user_id,
        ]);

        $query->andFilterWhere(['like', 'lt_notes', $this->lt_notes]);

        return $dataProvider;
    }


    public function searchDashboard($params)
    {
        $query = LeadTask::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['lt_date' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->status_not_in) {
            $query->joinWith(['ltLead' => function ($q) {
                $q->where(['NOT IN', 'leads.status', $this->status_not_in]);
            }]);
        }

        if ($this->status) {
            $query->joinWith(['ltLead' => function ($q) {
                $q->where(['IN', 'leads.status', $this->status]);
            }]);
        }

        $query->andWhere(['IS', 'lt_completed_dt', null]);

        // grid filtering conditions
        $query->andFilterWhere([
            'lt_lead_id' => $this->lt_lead_id,
            'lt_task_id' => $this->lt_task_id,
            'lt_user_id' => $this->lt_user_id,
            'lt_date' => $this->lt_date,
            'lt_completed_dt' => $this->lt_completed_dt,
            'lt_updated_dt' => $this->lt_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'lt_notes', $this->lt_notes]);

        return $dataProvider;
    }
}
