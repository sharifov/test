<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Conference;

/**
 * ConferenceSearch represents the model behind the search form of `common\models\Conference`.
 */
class ConferenceSearch extends Conference
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cf_id', 'cf_cr_id', 'cf_status_id', 'cf_created_user_id'], 'integer'],
            [['cf_sid', 'cf_options', 'cf_created_dt', 'cf_updated_dt'], 'safe'],
            [['cf_friendly_name', 'cf_call_sid'], 'string'],
            [['cf_start_dt', 'cf_end_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['cf_duration', 'integer'],
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
    
    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = Conference::find()->with(['createdUser']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cf_id' => SORT_DESC]],
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

        if ($this->cf_start_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cf_start_dt', $this->cf_start_dt, $user->timezone);
        }

        if ($this->cf_end_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cf_end_dt', $this->cf_end_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cf_id' => $this->cf_id,
            'cf_cr_id' => $this->cf_cr_id,
            'cf_status_id' => $this->cf_status_id,
            'cf_created_dt' => $this->cf_created_dt,
            'cf_updated_dt' => $this->cf_updated_dt,
            'cf_friendly_name' => $this->cf_friendly_name,
            'cf_created_user_id' => $this->cf_created_user_id,
            'cf_call_sid' => $this->cf_call_sid,
            'cf_duration' => $this->cf_duration,
        ]);

        $query->andFilterWhere(['like', 'cf_sid', $this->cf_sid])
            ->andFilterWhere(['like', 'cf_options', $this->cf_options]);

        return $dataProvider;
    }
}
