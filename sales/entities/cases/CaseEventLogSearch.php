<?php

namespace sales\entities\cases;

use sales\entities\cases\CaseEventLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CaseEventLogSearch represents the model behind the search form of `sales\entities\cases\CaseEventLog`.
 */
class CaseEventLogSearch extends CaseEventLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cel_id', 'cel_case_id'], 'integer'],
            [['cel_description', 'cel_data_json', 'cel_created_dt'], 'safe'],
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
        $query = CaseEventLog::find();

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
            'cel_id' => $this->cel_id,
            'cel_case_id' => $this->cel_case_id,
            'cel_created_dt' => $this->cel_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cel_description', $this->cel_description])
            ->andFilterWhere(['like', 'cel_data_json', $this->cel_data_json]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByCase($params)
    {
        $query = CaseEventLog::find()->where(['cel_case_id' => $params['case_id']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cel_id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 5,
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
            'cel_id' => $this->cel_id,
            //'cel_case_id' => $this->cel_case_id,
            'date(cel_created_dt)' => $this->cel_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cel_description', $this->cel_description])
            ->andFilterWhere(['like', 'cel_data_json', $this->cel_data_json]);

        return $dataProvider;
    }
}