<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserContactList;

/**
 * UserContactListSearch represents the model behind the search form of `common\models\UserContactList`.
 */
class UserContactListSearch extends UserContactList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ucl_user_id', 'ucl_client_id'], 'integer'],
            [['ucl_title', 'ucl_description', 'ucl_created_dt'], 'safe'],
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
        $query = UserContactList::find();

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
            'ucl_user_id' => $this->ucl_user_id,
            'ucl_client_id' => $this->ucl_client_id,
            'ucl_created_dt' => $this->ucl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ucl_title', $this->ucl_title])
            ->andFilterWhere(['like', 'ucl_description', $this->ucl_description]);

        return $dataProvider;
    }
}
