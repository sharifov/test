<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BillingInfo;

/**
 * BillingInfoSearch represents the model behind the search form of `common\models\BillingInfo`.
 */
class BillingInfoSearch extends BillingInfo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bi_id', 'bi_payment_method_id', 'bi_cc_id', 'bi_order_id', 'bi_status_id', 'bi_created_user_id', 'bi_updated_user_id'], 'integer'],
            [['bi_first_name', 'bi_last_name', 'bi_middle_name', 'bi_company_name', 'bi_address_line1', 'bi_address_line2', 'bi_city', 'bi_state', 'bi_country', 'bi_zip', 'bi_contact_phone', 'bi_contact_email', 'bi_contact_name', 'bi_created_dt', 'bi_updated_dt'], 'safe'],
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
        $query = BillingInfo::find();

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
            'bi_id' => $this->bi_id,
            'bi_payment_method_id' => $this->bi_payment_method_id,
            'bi_cc_id' => $this->bi_cc_id,
            'bi_order_id' => $this->bi_order_id,
            'bi_status_id' => $this->bi_status_id,
            'bi_created_user_id' => $this->bi_created_user_id,
            'bi_updated_user_id' => $this->bi_updated_user_id,
            'bi_created_dt' => $this->bi_created_dt,
            'bi_updated_dt' => $this->bi_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'bi_first_name', $this->bi_first_name])
            ->andFilterWhere(['like', 'bi_last_name', $this->bi_last_name])
            ->andFilterWhere(['like', 'bi_middle_name', $this->bi_middle_name])
            ->andFilterWhere(['like', 'bi_company_name', $this->bi_company_name])
            ->andFilterWhere(['like', 'bi_address_line1', $this->bi_address_line1])
            ->andFilterWhere(['like', 'bi_address_line2', $this->bi_address_line2])
            ->andFilterWhere(['like', 'bi_city', $this->bi_city])
            ->andFilterWhere(['like', 'bi_state', $this->bi_state])
            ->andFilterWhere(['like', 'bi_country', $this->bi_country])
            ->andFilterWhere(['like', 'bi_zip', $this->bi_zip])
            ->andFilterWhere(['like', 'bi_contact_phone', $this->bi_contact_phone])
            ->andFilterWhere(['like', 'bi_contact_email', $this->bi_contact_email])
            ->andFilterWhere(['like', 'bi_contact_name', $this->bi_contact_name]);

        return $dataProvider;
    }
}
