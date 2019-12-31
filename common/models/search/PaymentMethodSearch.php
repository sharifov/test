<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PaymentMethod;

/**
 * PaymentMethodSearch represents the model behind the search form of `common\models\PaymentMethod`.
 */
class PaymentMethodSearch extends PaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pm_id', 'pm_enabled', 'pm_category_id', 'pm_updated_user_id'], 'integer'],
            [['pm_name', 'pm_short_name', 'pm_updated_dt'], 'safe'],
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
        $query = PaymentMethod::find();

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
            'pm_id' => $this->pm_id,
            'pm_enabled' => $this->pm_enabled,
            'pm_category_id' => $this->pm_category_id,
            'pm_updated_user_id' => $this->pm_updated_user_id,
            'pm_updated_dt' => $this->pm_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pm_name', $this->pm_name])
            ->andFilterWhere(['like', 'pm_short_name', $this->pm_short_name]);

        return $dataProvider;
    }
}
