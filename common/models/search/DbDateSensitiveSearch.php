<?php

namespace common\models\search;

use common\models\DbDateSensitive;
use common\models\Employee;
use yii\data\ActiveDataProvider;

/**
 * DbDateSensitiveSearch represents the model behind the search form of `common\models\DbDateSensitive`.
 */
class DbDateSensitiveSearch extends DbDateSensitive
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dda_updated_user_id'], 'integer'],
            [['dda_name', 'dda_key', 'dda_updated_dt'], 'safe'],
            [['dda_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = DbDateSensitive::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['dda_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->dda_updated_dt) {
            $query->andFilterWhere(['>=', 'dda_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->dda_updated_dt))])
                ->andFilterWhere(['<=', 'dda_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->dda_updated_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere([
            'dda_updated_user_id' => $this->dda_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'dda_key', $this->dda_key])
            ->andFilterWhere(['like', 'dda_name', $this->dda_name]);

        return $dataProvider;
    }
}
