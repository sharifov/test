<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Setting;

/**
 * SettingSearch represents the model behind the search form of `common\models\Setting`.
 */
class SettingSearch extends Setting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['s_id', 's_updated_user_id', 's_category_id'], 'integer'],
            [['s_key', 's_name', 's_type', 's_value'], 'safe'],
            [['s_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = Setting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['s_updated_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 50,
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
            's_id' => $this->s_id,
            'DATE(s_updated_dt)' => $this->s_updated_dt,
            's_updated_user_id' => $this->s_updated_user_id,
            's_category_id' => $this->s_category_id,
        ]);

        $query->andFilterWhere(['like', 's_key', $this->s_key])
            ->andFilterWhere(['like', 's_name', $this->s_name])
            ->andFilterWhere(['like', 's_type', $this->s_type])
            ->andFilterWhere(['like', 's_value', $this->s_value]);

        return $dataProvider;
    }

    public function searchByCallRecording(): ActiveDataProvider
    {
        $query = static::find()->andWhere([
            's_key' => 'call_recording_disabled',
            's_value' => true,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'system-page',
                'pageSizeParam' => 'system-per-page',
            ],
            'sort' => [
                'sortParam' => 'system-sort',
            ],
        ]);
    }
}
