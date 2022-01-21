<?php

namespace src\model\clientChatDataRequest\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\clientChatDataRequest\entity\ClientChatDataRequest;

/**
 * ClientChatDataRequestSearch represents the model behind the search form of `src\model\clientChatDataRequest\entity\ClientChatDataRequest`.
 */
class ClientChatDataRequestSearch extends ClientChatDataRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccdr_id', 'ccdr_chat_id'], 'integer'],
            [['ccdr_data_json', 'ccdr_created_dt'], 'safe'],
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
        $query = ClientChatDataRequest::find();

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
            'ccdr_id' => $this->ccdr_id,
            'ccdr_chat_id' => $this->ccdr_chat_id,
            'ccdr_created_dt' => $this->ccdr_created_dt,
        ]);

        $query->andFilterWhere(['like', 'ccdr_data_json', $this->ccdr_data_json]);

        return $dataProvider;
    }
}
