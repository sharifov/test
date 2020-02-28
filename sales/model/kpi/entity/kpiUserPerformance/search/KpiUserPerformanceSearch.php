<?php

namespace sales\model\kpi\entity\kpiUserPerformance\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\kpi\entity\kpiUserPerformance\KpiUserPerformance;

/**
 * KpiUserPerformanceSearch represents the model behind the search form of `sales\model\kpi\entity\KpiUserPerformance`.
 */
class KpiUserPerformanceSearch extends KpiUserPerformance
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id', 'up_year', 'up_month', 'up_performance', 'up_created_user_id', 'up_updated_user_id'], 'integer'],
            [['up_created_dt', 'up_updated_dt'], 'safe'],
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
        $query = KpiUserPerformance::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'=> ['defaultOrder' => ['up_created_dt' => SORT_DESC, 'up_updated_dt' => SORT_DESC]],
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

        // grid filtering conditions
        $query->andFilterWhere([
            'up_user_id' => $this->up_user_id,
            'up_year' => $this->up_year,
            'up_month' => $this->up_month,
            'up_performance' => $this->up_performance,
            'up_created_user_id' => $this->up_created_user_id,
            'up_updated_user_id' => $this->up_updated_user_id,
            'date_format(up_created_dt, "%Y-%m-%d")' => $this->up_created_dt,
            'date_format(up_updated_dt, "%Y-%m-%d")' => $this->up_updated_dt,
        ]);

        return $dataProvider;
    }
}
