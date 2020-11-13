<?php

namespace sales\model\clientChatMessage\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChatMessage\entity\ClientChatMessage;

/**
 * ClientChatMessageSearch represents the model behind the search form of `sales\model\clientChatMessage\entity\ClientChatMessage`.
 */
class ClientChatMessageSearch extends ClientChatMessage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccm_id', 'ccm_client_id', 'ccm_user_id', 'ccm_cch_id'], 'integer'],
            [['ccm_rid', 'ccm_body'], 'safe'],
            [['ccm_sent_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = ClientChatMessage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ccm_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ccm_id' => $this->ccm_id,
            'ccm_client_id' => $this->ccm_client_id,
            'ccm_user_id' => $this->ccm_user_id,
            'DATE(ccm_sent_dt)' => $this->ccm_sent_dt,
            'ccm_cch_id' => $this->ccm_cch_id,
        ]);

        $query->andFilterWhere(['ilike', 'ccm_rid', $this->ccm_rid])
            ->andFilterWhere(['ilike', 'ccm_body', $this->ccm_body]);

        return $dataProvider;
    }

    public function history(array $params): ActiveDataProvider
	{
		$query = self::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'=> ['defaultOrder' => ['ccm_sent_dt' => SORT_ASC]],
			'pagination' => [
				'pageSize' => null,
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
			'ccm_id' => $this->ccm_id,
			'ccm_client_id' => $this->ccm_client_id,
			'ccm_user_id' => $this->ccm_user_id,
			'ccm_sent_dt' => $this->ccm_sent_dt,
		]);

		$query->andFilterWhere(['ilike', 'ccm_rid', $this->ccm_rid])
			->andFilterWhere(['ilike', 'ccm_body', $this->ccm_body]);

		return $dataProvider;
	}
}
