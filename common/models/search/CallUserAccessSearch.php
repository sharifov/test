<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CallUserAccess;

/**
 * CallUserAccessSearch represents the model behind the search form of `common\models\CallUserAccess`.
 */
class CallUserAccessSearch extends CallUserAccess
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cua_call_id', 'cua_user_id', 'cua_status_id'], 'integer'],
            [['cua_created_dt', 'cua_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cua_priority', 'integer'],
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
        $query = CallUserAccess::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cua_created_dt' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cua_created_dt) {
            $query->andFilterWhere(['>=', 'cua_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cua_created_dt))])
                ->andFilterWhere(['<=', 'cua_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cua_created_dt) + 3600 * 24)]);
        }

        if ($this->cua_updated_dt) {
            $query->andFilterWhere(['>=', 'cua_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cua_updated_dt))])
                ->andFilterWhere(['<=', 'cua_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cua_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cua_call_id' => $this->cua_call_id,
            'cua_user_id' => $this->cua_user_id,
            'cua_status_id' => $this->cua_status_id,
            'cua_priority' => $this->cua_priority,
            //'cua_created_dt' => $this->cua_created_dt,
            //'cua_updated_dt' => $this->cua_updated_dt,
        ]);

        return $dataProvider;
    }
}
