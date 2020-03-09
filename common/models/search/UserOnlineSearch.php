<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserOnline;

/**
 * UserOnlineSearch represents the model behind the search form of `common\models\UserOnline`.
 */
class UserOnlineSearch extends UserOnline
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uo_user_id'], 'integer'],
            [['uo_updated_dt'], 'safe'],
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
        $query = UserOnline::find();

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
            'uo_user_id' => $this->uo_user_id,
            'uo_updated_dt' => $this->uo_updated_dt,
        ]);

        return $dataProvider;
    }
}
