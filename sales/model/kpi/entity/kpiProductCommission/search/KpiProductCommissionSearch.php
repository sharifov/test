<?php

namespace sales\model\kpi\entity\kpiProductCommission\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\kpi\entity\kpiProductCommission\KpiProductCommission;

/**
 * KpiProductCommissionSearch represents the model behind the search form of `sales\model\kpi\entity\kpiProductCommission\KpiProductCommission`.
 */
class KpiProductCommissionSearch extends KpiProductCommission
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pc_product_type_id', 'pc_performance', 'pc_commission_percent', 'pc_created_user_id', 'pc_updated_user_id'], 'integer'],
            [['pc_created_dt', 'pc_updated_dt'], 'safe'],
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
        $query = KpiProductCommission::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'=> ['defaultOrder' => ['pc_product_type_id' => SORT_DESC, 'pc_performance' => SORT_DESC, 'pc_commission_percent' => SORT_DESC]],
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
            'pc_product_type_id' => $this->pc_product_type_id,
            'pc_performance' => $this->pc_performance,
            'pc_commission_percent' => $this->pc_commission_percent,
            'pc_created_user_id' => $this->pc_created_user_id,
            'pc_updated_user_id' => $this->pc_updated_user_id,
            'date_format(pc_created_dt, "%Y-%m-%d")' => $this->pc_created_dt,
            'date_format(pc_updated_dt, "%Y-%m-%d")' => $this->pc_updated_dt,
        ]);

        return $dataProvider;
    }
}
