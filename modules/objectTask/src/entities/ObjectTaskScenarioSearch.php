<?php

namespace modules\objectTask\src\entities;

use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * ObjectTaskScenarioSearch represents the model behind the search form of `modules\objectTask\src\entities\ObjectTaskScenario`.
 */
class ObjectTaskScenarioSearch extends ObjectTaskScenario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ots_id', 'ots_updated_user_id', 'ots_enable'], 'integer'],
            [['ots_key', 'ots_data_json', 'ots_updated_dt'], 'safe'],
        ];
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
        $query = ObjectTaskScenario::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ots_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->ots_updated_dt) {
            $query->andFilterWhere(['>=', 'ots_updated_dt',
                Employee::convertTimeFromUserDtToUTC(strtotime($this->ots_updated_dt))])
                ->andFilterWhere(['<=', 'ots_updated_dt',
                    Employee::convertTimeFromUserDtToUTC(strtotime($this->ots_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ots_id' => $this->ots_id,
            'ots_updated_user_id' => $this->ots_updated_user_id,
            'ots_enable' => $this->ots_enable,
        ]);

        $query->andFilterWhere(['like', 'ots_key', $this->ots_key])
            ->andFilterWhere(['like', 'ots_data_json', $this->ots_data_json]);

        return $dataProvider;
    }
}
