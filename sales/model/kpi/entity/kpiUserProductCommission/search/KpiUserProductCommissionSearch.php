<?php

namespace sales\model\kpi\entity\kpiUserProductCommission\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission;

/**
 * KpiUserProductCommissionSearch represents the model behind the search form of `sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission`.
 */
class KpiUserProductCommissionSearch extends KpiUserProductCommission
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month', 'upc_performance', 'upc_commission_percent', 'upc_created_user_id', 'upc_updated_user_id'], 'integer'],
            [['upc_created_dt', 'upc_updated_dt'], 'safe'],
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
        $query = KpiUserProductCommission::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'upc_product_type_id' => $this->upc_product_type_id,
            'upc_user_id' => $this->upc_user_id,
            'upc_year' => $this->upc_year,
            'upc_month' => $this->upc_month,
            'upc_performance' => $this->upc_performance,
            'upc_commission_percent' => $this->upc_commission_percent,
            'upc_created_user_id' => $this->upc_created_user_id,
            'upc_updated_user_id' => $this->upc_updated_user_id,
            'date_format(upc_created_dt, "%Y-%m-%d")' => $this->upc_created_dt,
            'date_format(upc_updated_dt, "%Y-%m-%d")' => $this->upc_updated_dt,
        ]);

        return $dataProvider;
    }
}
