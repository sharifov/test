<?php

namespace sales\entities\cases;

use yii\data\ActiveDataProvider;

/**
 * Class CasesSearch
 */
class CasesSearch extends Cases
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['cs_id', 'integer'],

            ['cs_gid', 'string'],

            ['cs_category', 'integer'],

            ['cs_status', 'integer'],

            ['cs_user_id', 'integer'],

            ['cs_lead_id', 'integer'],

            ['cs_call_id', 'integer'],

            ['cs_dep_id', 'integer'],

            ['cs_subject', 'string'],

            ['cs_description', 'string'],

            ['cs_created_dt', 'string'],

        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Cases::find();

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
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_category' => $this->cs_category,
            'cs_status' => $this->cs_status,
            'cs_user_id' => $this->cs_user_id,
            'cs_lead_id' => $this->cs_lead_id,
            'cs_call_id' => $this->cs_call_id,
            'cs_dep_id' => $this->cs_dep_id,
        ]);


        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)'=> date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject])
            ->andFilterWhere(['like', 'cs_description', $this->cs_description]);

        return $dataProvider;
    }
}
