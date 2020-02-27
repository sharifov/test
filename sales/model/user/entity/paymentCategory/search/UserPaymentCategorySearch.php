<?php

namespace sales\model\user\entity\paymentCategory\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\entity\paymentCategory\UserPaymentCategory;

/**
 * Class UserPaymentCategorySearch
 * @package sales\model\user\entity\paymentCategory\search
 */
class UserPaymentCategorySearch extends UserPaymentCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['upc_id', 'upc_enabled', 'upc_created_user_id', 'upc_updated_user_id'], 'integer'],
            [['upc_name', 'upc_description', 'upc_created_dt', 'upc_updated_dt'], 'safe'],
        ];
    }

	/**
	 * @return array
	 */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
	{
        $query = UserPaymentCategory::find();

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
            'upc_id' => $this->upc_id,
            'upc_enabled' => $this->upc_enabled,
            'upc_created_user_id' => $this->upc_created_user_id,
            'upc_updated_user_id' => $this->upc_updated_user_id,
            'date_format(upc_created_dt, "%Y-%m-%d")' => $this->upc_created_dt,
            'date_format(upc_updated_dt, "%Y-%m-%d")' => $this->upc_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'upc_name', $this->upc_name])
            ->andFilterWhere(['like', 'upc_description', $this->upc_description]);

        return $dataProvider;
    }
}
