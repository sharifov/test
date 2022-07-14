<?php

namespace src\model\clientUserReturn\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\clientUserReturn\entity\ClientUserReturn;

/**
 * ClientUserReturnSearch represents the model behind the search form of `src\model\clientUserReturn\entity\ClientUserReturn`.
 */
class ClientUserReturnSearch extends ClientUserReturn
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cur_client_id', 'cur_user_id'], 'integer'],
            [['cur_created_dt'], 'safe'],
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
        $query = ClientUserReturn::find();

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
            'cur_client_id' => $this->cur_client_id,
            'cur_user_id' => $this->cur_user_id,
            'cur_created_dt' => $this->cur_created_dt,
        ]);

        return $dataProvider;
    }
}
