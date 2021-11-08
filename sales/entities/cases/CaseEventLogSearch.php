<?php

namespace sales\entities\cases;

use common\models\Employee;
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
            [['cel_id', 'cel_case_id', 'cel_category_id', 'cel_type_id'], 'integer'],
            [['cel_data_json'], 'safe'],
            ['cel_description', 'string'],
            ['cel_created_dt', 'date', 'format' => 'php:Y-m-d'],
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
            'sort' => [
                'defaultOrder' => [
                    'cel_id' => SORT_DESC,
                ]
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
            'cel_case_id' => $this->cel_case_id,
            'date(cel_created_dt)' => $this->cel_created_dt,
            'cel_category_id' => $this->cel_category_id,
            'cel_type_id' => $this->cel_type_id
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
     * @param Employee $employee
     * @return ActiveDataProvider
     */
    public function searchByCase($params, Employee $employee)
    {
        $query = CaseEventLog::find()->where(['cel_case_id' => $params['case_id']]);

        if (!$employee->isAdmin() && !$employee->isSuperAdmin()) {
            $query->andWhere(['<>', 'cel_category_id', CaseEventLog::CATEGORY_DEBUG]);
            $query->orWhere(['cel_category_id' => null]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cel_id' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
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
