<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadQcall;
use yii\db\Expression;

/**
 * LeadQcallSearch represents the model behind the search form of `common\models\LeadQcall`.
 *
 * @property string $current_dt
 */
class LeadQcallSearch extends LeadQcall
{
    public $current_dt;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lqc_lead_id', 'lqc_weight'], 'integer'],

            [['lqc_dt_from', 'lqc_dt_to', 'current_dt'], 'safe'],
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
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchList($params): ActiveDataProvider
    {
        $query = LeadQcall::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['lqc_weight' => SORT_ASC, 'lqc_dt_from' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->current_dt) {
            $current_dt = Employee::convertTimeFromUserDtToUTC(strtotime($this->current_dt));
            //echo $current_dt; exit;
            $query->andWhere(['<=', 'lqc_dt_from', $current_dt]);
            //$query->andWhere(['>=', 'lqc_dt_to', $current_dt]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lqc_lead_id' => $this->lqc_lead_id,
            'lqc_dt_from' => $this->lqc_dt_from,
            'lqc_dt_to' => $this->lqc_dt_to,
            'lqc_weight' => $this->lqc_weight,
        ]);

        $query->with(['lqcLead', 'lqcLead.project', 'lqcLead.source', 'lqcLead.employee']);

        return $dataProvider;
    }
}
