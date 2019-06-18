<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserConnection;

/**
 * UserConnectionSearch represents the model behind the search form of `common\models\UserConnection`.
 */
class UserConnectionSearch extends UserConnection
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uc_id', 'uc_connection_id', 'uc_user_id', 'uc_lead_id'], 'integer'],
            [['uc_user_agent', 'uc_controller_id', 'uc_action_id', 'uc_page_url', 'uc_ip', 'uc_created_dt'], 'safe'],
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
        $query = UserConnection::find();

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

        if (isset($params['UserConnectionSearch']['uc_created_dt'])) {
            $query->andFilterWhere(['=','DATE(uc_created_dt)', $this->uc_created_dt]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'uc_id' => $this->uc_id,
            'uc_connection_id' => $this->uc_connection_id,
            'uc_user_id' => $this->uc_user_id,
            'uc_lead_id' => $this->uc_lead_id,
        ]);

        $query->andFilterWhere(['like', 'uc_user_agent', $this->uc_user_agent])
            ->andFilterWhere(['like', 'uc_controller_id', $this->uc_controller_id])
            ->andFilterWhere(['like', 'uc_action_id', $this->uc_action_id])
            ->andFilterWhere(['like', 'uc_page_url', $this->uc_page_url])
            ->andFilterWhere(['like', 'uc_ip', $this->uc_ip]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchUserCallMap($params)
    {
        $query = UserConnection::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->select(['uc_user_id']); //'cnt' => 'COUNT(*)',
        $query->groupBy(['uc_user_id']);
        //$query->with(['ucUser']);

        return $dataProvider;
    }


}
