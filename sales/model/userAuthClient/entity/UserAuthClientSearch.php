<?php

namespace sales\model\userAuthClient\entity;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\userAuthClient\entity\UserAuthClient;

/**
 * UserAuthClientSearch represents the model behind the search form of `sales\model\userAuthClient\entity\UserAuthClient`.
 */
class UserAuthClientSearch extends UserAuthClient
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uac_id', 'uac_user_id'], 'integer'],
            [['uac_source', 'uac_source_id', 'uac_email', 'uac_ip', 'uac_useragent', 'uac_created_dt'], 'safe'],
            [['uac_created_dt'], 'date', 'format' => 'Y-m-d'],
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
        $query = UserAuthClient::find();

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
            'uac_id' => $this->uac_id,
            'uac_user_id' => $this->uac_user_id,
            'date(uac_created_dt)' => $this->uac_created_dt,
            'uac_source' => $this->uac_source
        ]);

        $query->andFilterWhere(['like', 'uac_source_id', $this->uac_source_id])
            ->andFilterWhere(['like', 'uac_email', $this->uac_email])
            ->andFilterWhere(['like', 'uac_ip', $this->uac_ip])
            ->andFilterWhere(['like', 'uac_useragent', $this->uac_useragent]);

        return $dataProvider;
    }
}
