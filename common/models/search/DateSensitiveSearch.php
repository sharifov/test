<?php

namespace common\models\search;

use common\models\DateSensitive;
use yii\data\ActiveDataProvider;

/**
 * DateSensitiveSearch represents the model behind the search form of `common\models\DateSensitive`.
 */
class DateSensitiveSearch extends DateSensitive
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['da_created_user_id', 'da_updated_user_id'], 'integer'],
            [['da_name', 'da_key'], 'safe'],
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
        $query = DateSensitive::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['da_id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'da_created_user_id' => $this->da_created_user_id,
            'da_updated_user_id' => $this->da_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'da_key', $this->da_key])
            ->andFilterWhere(['like', 'da_name', $this->da_name]);

        return $dataProvider;
    }
}
