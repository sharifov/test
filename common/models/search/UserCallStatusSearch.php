<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserCallStatus;

/**
 * UserCallStatusSearch represents the model behind the search form of `common\models\UserCallStatus`.
 */
class UserCallStatusSearch extends UserCallStatus
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['us_id', 'us_type_id', 'us_user_id'], 'integer'],
            [['us_created_dt'], 'safe'],
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
        $query = UserCallStatus::find()->with('usUser');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['us_id' => SORT_DESC]
            ],
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
            'us_id' => $this->us_id,
            'us_type_id' => $this->us_type_id,
            'us_user_id' => $this->us_user_id,
            'us_created_dt' => $this->us_created_dt,
        ]);

        return $dataProvider;
    }
}
