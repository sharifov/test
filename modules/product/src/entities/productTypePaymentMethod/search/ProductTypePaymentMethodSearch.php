<?php

namespace modules\product\src\entities\productTypePaymentMethod\search;

use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethod;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductTypePaymentMethodSearch represents the model behind the search form of `modules\product\src\entities\productTypePaymentMethod\productTypePaymentMethod`.
 */
class ProductTypePaymentMethodSearch extends ProductTypePaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ptpm_produt_type_id', 'ptpm_payment_method_id', 'ptpm_payment_fee_percent', 'ptpm_enabled', 'ptpm_default', 'ptpm_created_user_id', 'ptpm_updated_user_id'], 'integer'],
            [['ptpm_payment_fee_amount'], 'number'],
            [['ptpm_created_dt', 'ptpm_updated_dt'], 'safe'],
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
        $query = ProductTypePaymentMethod::find();

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
            'ptpm_produt_type_id' => $this->ptpm_produt_type_id,
            'ptpm_payment_method_id' => $this->ptpm_payment_method_id,
            'ptpm_payment_fee_percent' => $this->ptpm_payment_fee_percent,
            'ptpm_payment_fee_amount' => $this->ptpm_payment_fee_amount,
            'ptpm_enabled' => $this->ptpm_enabled,
            'ptpm_default' => $this->ptpm_default,
            'ptpm_created_user_id' => $this->ptpm_created_user_id,
            'ptpm_updated_user_id' => $this->ptpm_updated_user_id,
            'ptpm_created_dt' => $this->ptpm_created_dt,
            'ptpm_updated_dt' => $this->ptpm_updated_dt,
        ]);

        return $dataProvider;
    }
}
