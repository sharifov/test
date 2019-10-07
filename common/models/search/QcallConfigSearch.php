<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QcallConfig;

/**
 * QcallConfigSearch represents the model behind the search form of `common\models\QcallConfig`.
 */
class QcallConfigSearch extends QcallConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qc_status_id', 'qc_call_att', 'qc_client_time_enable', 'qc_time_from', 'qc_time_to', 'qc_created_user_id', 'qc_updated_user_id'], 'integer'],
            [['qc_created_dt', 'qc_updated_dt'], 'safe'],
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
        $query = QcallConfig::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['qc_status_id' => SORT_ASC, 'qc_call_att' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 40,
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
            'qc_status_id' => $this->qc_status_id,
            'qc_call_att' => $this->qc_call_att,
            'qc_client_time_enable' => $this->qc_client_time_enable,
            'qc_time_from' => $this->qc_time_from,
            'qc_time_to' => $this->qc_time_to,
            'qc_created_dt' => $this->qc_created_dt,
            'qc_updated_dt' => $this->qc_updated_dt,
            'qc_created_user_id' => $this->qc_created_user_id,
            'qc_updated_user_id' => $this->qc_updated_user_id,
        ]);

        return $dataProvider;
    }
}
