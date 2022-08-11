<?php

namespace common\models\search;

use common\models\Employee;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserParams;

/**
 * UserParamsSearch represents the model behind the search form of `common\models\UserParams`.
 */
class UserParamsSearch extends UserParams
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id', 'up_commission_percent', 'up_updated_user_id', 'up_bonus_active', 'up_inbox_show_limit_leads', 'up_business_inbox_show_limit_leads', 'up_default_take_limit_leads', 'up_min_percent_for_take_leads', 'up_call_expert_limit'], 'integer'],
            [['up_base_amount'], 'number'],
            [['up_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['up_call_user_level', 'integer'],
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
        $query = UserParams::find()->with('upUpdatedUser');

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

        if ($this->up_updated_dt) {
            $query->andFilterWhere(['>=', 'up_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->up_updated_dt))])
                ->andFilterWhere(['<=', 'up_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->up_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'up_user_id' => $this->up_user_id,
            'up_commission_percent' => $this->up_commission_percent,
            'up_base_amount' => $this->up_base_amount,
            'up_bonus_active' => $this->up_bonus_active,
            'up_updated_user_id' => $this->up_updated_user_id,
            'up_inbox_show_limit_leads' => $this->up_inbox_show_limit_leads,
            'up_business_inbox_show_limit_leads' => $this->up_business_inbox_show_limit_leads,
            'up_default_take_limit_leads' => $this->up_default_take_limit_leads,
            'up_min_percent_for_take_leads' => $this->up_min_percent_for_take_leads,
            'up_call_expert_limit' => $this->up_call_expert_limit,
            'up_call_user_level' => $this->up_call_user_level,
        ]);

        return $dataProvider;
    }
}
