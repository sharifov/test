<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CreditCard;

/**
 * CreditCardSearch represents the model behind the search form of `common\models\CreditCard`.
 */
class CreditCardSearch extends CreditCard
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cc_id', 'cc_expiration_month', 'cc_expiration_year', 'cc_type_id', 'cc_status_id', 'cc_is_expired', 'cc_created_user_id', 'cc_updated_user_id'], 'integer'],
            [['cc_number', 'cc_display_number', 'cc_holder_name', 'cc_cvv', 'cc_created_dt', 'cc_updated_dt'], 'safe'],
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
        $query = CreditCard::find();

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
            'cc_id' => $this->cc_id,
            'cc_expiration_month' => $this->cc_expiration_month,
            'cc_expiration_year' => $this->cc_expiration_year,
            'cc_type_id' => $this->cc_type_id,
            'cc_status_id' => $this->cc_status_id,
            'cc_is_expired' => $this->cc_is_expired,
            'cc_created_user_id' => $this->cc_created_user_id,
            'cc_updated_user_id' => $this->cc_updated_user_id,
            'cc_created_dt' => $this->cc_created_dt,
            'cc_updated_dt' => $this->cc_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cc_number', $this->cc_number])
            ->andFilterWhere(['like', 'cc_display_number', $this->cc_display_number])
            ->andFilterWhere(['like', 'cc_holder_name', $this->cc_holder_name])
            ->andFilterWhere(['like', 'cc_cvv', $this->cc_cvv]);

        return $dataProvider;
    }
}
