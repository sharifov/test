<?php

namespace sales\model\authClient\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\authClient\entity\AuthClient;

/**
 * AuthClientSearch represents the model behind the search form of `sales\model\authClient\entity\AuthClient`.
 */
class AuthClientSearch extends AuthClient
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ac_id', 'ac_user_id'], 'integer'],
            [['ac_source', 'ac_source_id', 'ac_email', 'ac_ip', 'ac_useragent', 'ac_created_dt'], 'safe'],
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
        $query = AuthClient::find();

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
            'ac_id' => $this->ac_id,
            'ac_user_id' => $this->ac_user_id,
            'date(ac_created_dt)' => $this->ac_created_dt,
            'ac_source' => $this->ac_source
        ]);

        $query->andFilterWhere(['like', 'ac_source_id', $this->ac_source_id])
            ->andFilterWhere(['like', 'ac_email', $this->ac_email])
            ->andFilterWhere(['like', 'ac_ip', $this->ac_ip])
            ->andFilterWhere(['like', 'ac_useragent', $this->ac_useragent]);

        return $dataProvider;
    }
}
