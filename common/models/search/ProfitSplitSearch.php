<?php

namespace common\models\search;

use common\models\Employee;
use common\models\ProfitSplit;
use yii\data\ActiveDataProvider;

class ProfitSplitSearch extends ProfitSplit
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ps_id', 'ps_lead_id', 'ps_user_id', 'ps_percent', 'ps_amount', 'ps_updated_user_id'], 'integer'],
            [['ps_updated_dt'], 'safe'],
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
        $query = ProfitSplit::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ps_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ps_updated_dt) {
            $query->andFilterWhere(['>=', 'dda_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ps_updated_dt))])
                ->andFilterWhere(['<=', 'dda_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ps_updated_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere([
            'ps_id' => $this->ps_id,
            'ps_lead_id' => $this->ps_lead_id,
            'ps_user_id' => $this->ps_user_id,
            'ps_percent' => $this->ps_percent,
            'ps_updated_user_id' => $this->ps_updated_user_id,
        ]);

        return $dataProvider;
    }
}
