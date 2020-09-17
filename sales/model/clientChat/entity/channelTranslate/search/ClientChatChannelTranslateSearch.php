<?php

namespace sales\model\clientChat\entity\channelTranslate\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate;

/**
 * ClientChatChannelTranslateSearch represents the model behind the search form of `sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate`.
 */
class ClientChatChannelTranslateSearch extends ClientChatChannelTranslate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ct_channel_id', 'ct_created_user_id', 'ct_updated_user_id'], 'integer'],
            [['ct_language_id', 'ct_name', 'ct_created_dt', 'ct_updated_dt'], 'safe'],
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
        $query = ClientChatChannelTranslate::find();

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
            'ct_channel_id' => $this->ct_channel_id,
            'ct_created_user_id' => $this->ct_created_user_id,
            'ct_updated_user_id' => $this->ct_updated_user_id,
            'ct_created_dt' => $this->ct_created_dt,
            'ct_updated_dt' => $this->ct_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ct_language_id', $this->ct_language_id])
            ->andFilterWhere(['like', 'ct_name', $this->ct_name]);

        return $dataProvider;
    }
}
