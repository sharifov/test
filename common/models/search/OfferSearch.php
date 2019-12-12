<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Offer;

/**
 * OfferSearch represents the model behind the search form of `common\models\Offer`.
 */
class OfferSearch extends Offer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['of_id', 'of_lead_id', 'of_status_id', 'of_owner_user_id', 'of_created_user_id', 'of_updated_user_id'], 'integer'],
            [['of_gid', 'of_uid', 'of_name', 'of_created_dt', 'of_updated_dt'], 'safe'],
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
        $query = Offer::find();

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
            'of_id' => $this->of_id,
            'of_lead_id' => $this->of_lead_id,
            'of_status_id' => $this->of_status_id,
            'of_owner_user_id' => $this->of_owner_user_id,
            'of_created_user_id' => $this->of_created_user_id,
            'of_updated_user_id' => $this->of_updated_user_id,
            'of_created_dt' => $this->of_created_dt,
            'of_updated_dt' => $this->of_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'of_gid', $this->of_gid])
            ->andFilterWhere(['like', 'of_uid', $this->of_uid])
            ->andFilterWhere(['like', 'of_name', $this->of_name]);

        return $dataProvider;
    }
}
