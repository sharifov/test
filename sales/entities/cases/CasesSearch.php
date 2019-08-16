<?php

namespace sales\entities\cases;

use common\models\Employee;
use common\models\Lead;
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

            ['cs_gid', 'string'],

            ['cs_project_id', 'integer'],

            ['cs_subject', 'string'],

            ['cs_category', 'string'],

            ['cs_status', 'integer'],

            ['cs_user_id', 'string'],

            ['cs_lead_id', 'string'],

            ['cs_dep_id', 'integer'],

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
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category' => $this->cs_category,
            'cs_status' => $this->cs_status,
            'cs_dep_id' => $this->cs_dep_id,
        ]);

        if ($this->cs_user_id) {
            $query->andWhere(['cs_user_id' => Employee::find()->select('id')->andWhere(['like', 'username', $this->cs_user_id])]);
        }

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_gid' => 'GID',
            'cs_project_id' => 'Project',
            'cs_subject' => 'Subject',
            'cs_category' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead',
            'cs_dep_id' => 'Department',
            'cs_created_dt' => 'Created',
        ];
    }
}
