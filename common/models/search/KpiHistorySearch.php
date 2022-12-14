<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\KpiHistory;
use yii\helpers\VarDumper;

/**
 * KpiHistorySearch represents the model behind the search form of `common\models\KpiHistory`.
 *
 * @property $usersIdsInCommonGroups
 */
class KpiHistorySearch extends KpiHistory
{
    public $usersIdsInCommonGroups;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kh_id', 'kh_user_id', 'kh_super_id', 'kh_bonus_active', 'kh_commission_percent'], 'integer'],
            [['kh_agent_approved_dt', 'kh_super_approved_dt', 'kh_description'], 'safe'],
            [['kh_base_amount', 'kh_profit_bonus', 'kh_manual_bonus', 'kh_estimation_profit'], 'number'],
            [['kh_created_dt', 'kh_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['kh_date_dt'], 'date', 'format' => 'php:M-Y'],
            ['usersIdsInCommonGroups', 'safe']
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
        $query = KpiHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['kh_date_dt' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'kh_id' => $this->kh_id,
            'kh_user_id' => $this->kh_user_id,
            'kh_created_dt' => $this->kh_created_dt,
            'kh_updated_dt' => $this->kh_updated_dt,
            'kh_super_id' => $this->kh_super_id,
            'kh_base_amount' => $this->kh_base_amount,
            'kh_bonus_active' => $this->kh_bonus_active,
            'kh_commission_percent' => $this->kh_commission_percent,
            'kh_profit_bonus' => $this->kh_profit_bonus,
            'kh_manual_bonus' => $this->kh_manual_bonus,
            'kh_estimation_profit' => $this->kh_estimation_profit,
        ]);

        if ($this->kh_date_dt) {
            $start = \DateTime::createFromFormat('M-Y', $this->kh_date_dt);
            $start = $start->modify('first day of this month');
            $end = clone $start;
            $end->modify('last day of this month');
            $query->andFilterWhere(['BETWEEN', 'DATE(kh_date_dt)', $start->format('Y-m-d'), $end->format('Y-m-d')]);
        }
        $query->andFilterWhere(['like', 'kh_description', $this->kh_description]);

        if ($this->kh_agent_approved_dt) {
            $from = Employee::convertTimeFromUserDtToUTC(strtotime($this->kh_agent_approved_dt));
            $to = (new \DateTime($from . ' +1 day'))->format('Y-m-d H:i:s');
            $query->andWhere(['>=', 'kh_agent_approved_dt', $from]);
            $query->andWhere(['<', 'kh_agent_approved_dt', $to]);
        }
        if ($this->kh_super_approved_dt) {
            $from = Employee::convertTimeFromUserDtToUTC(strtotime($this->kh_super_approved_dt));
            $to = (new \DateTime($from . ' +1 day'))->format('Y-m-d H:i:s');
            $query->andWhere(['>=', 'kh_super_approved_dt', $from]);
            $query->andWhere(['<', 'kh_super_approved_dt', $to]);
        }

        if ($this->usersIdsInCommonGroups) {
            $query->andFilterWhere(['kh_user_id' => $this->usersIdsInCommonGroups]);
        }

        return $dataProvider;
    }
}
