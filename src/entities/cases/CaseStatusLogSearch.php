<?php

namespace src\entities\cases;

use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * Class CaseStatusLogSearch
 *
 * @property array $statuses
 * @property string $created_date_from
 * @property string $created_date_to
 * @property int $project_id
 * @property int $department_id
 */
class CaseStatusLogSearch extends CaseStatusLog
{
    public $statuses = [];

    public $created_date_from;

    public $created_date_to;

    public $project_id;

    public $department_id;

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return parent::attributeLabels();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['csl_id', 'integer'],

            ['csl_from_status', 'integer'],

            ['csl_to_status', 'integer'],

            ['statuses', 'safe'],

            ['csl_owner_id', 'integer'],

            ['csl_created_user_id', 'integer'],

            ['csl_case_id', 'integer'],

            [['csl_start_dt', 'csl_end_dt'], 'date', 'format' => 'php:Y-m-d'],

            [['created_date_from', 'created_date_to'], 'string'],

            ['project_id', 'integer'],

            ['department_id', 'integer']

        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = CaseStatusLog::find();
        $query->joinWith(['cases']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['csl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
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
            'csl_id' => $this->csl_id,
            'csl_case_id' => $this->csl_case_id,
            'csl_from_status' => $this->csl_from_status,
            'csl_to_status' => $this->csl_to_status,
            'csl_owner_id' => $this->csl_owner_id,
            'csl_created_user_id' => $this->csl_created_user_id,
        ]);

        if ($this->created_date_from || $this->created_date_to) {
            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(csl_start_dt)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(csl_start_dt)', date('Y-m-d', strtotime($this->created_date_to))]);
            }
        }

        if ($this->csl_start_dt) {
            if ($this->csl_start_dt) {
                $query->andFilterWhere(['>=', 'csl_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->csl_start_dt))])
                    ->andFilterWhere(['<=', 'csl_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->csl_start_dt) + 3600 * 24)]);
            }
        }

        if ($this->csl_end_dt) {
                $query->andFilterWhere(['>=', 'csl_end_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->csl_end_dt))])
                    ->andFilterWhere(['<=', 'csl_end_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->csl_end_dt) + 3600 * 24)]);
        }

        if ($this->statuses && is_array($this->statuses)) {
            $query->andWhere(['csl_to_status' => $this->statuses]);
        }

        if ($this->project_id) {
            $query->andFilterWhere(['=', 'cases.cs_project_id', $this->project_id]);
        }

        if ($this->department_id) {
            $query->andFilterWhere(['=', 'cases.cs_dep_id', $this->department_id]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchByCase($params): ActiveDataProvider
    {
        $query = CaseStatusLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['csl_id' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 10,
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
            'csl_case_id' => $this->csl_case_id,
            'csl_from_status' => $this->csl_from_status,
            'csl_to_status' => $this->csl_to_status,
            'csl_owner_id' => $this->csl_owner_id,
        ]);

        if ($this->created_date_from || $this->created_date_to) {
            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(csl_start_dt)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(csl_start_dt)', date('Y-m-d', strtotime($this->created_date_to))]);
            }
        }

        if ($this->csl_start_dt) {
            $query->andFilterWhere(['DATE(csl_start_dt)' => date('Y-m-d', strtotime($this->csl_start_dt))]);
        }

        if ($this->csl_end_dt) {
            $query->andFilterWhere(['DATE(csl_end_dt)' => date('Y-m-d', strtotime($this->csl_end_dt))]);
        }

        if ($this->statuses && is_array($this->statuses)) {
            $query->andWhere(['csl_to_status' => $this->statuses]);
        }

        return $dataProvider;
    }
}
