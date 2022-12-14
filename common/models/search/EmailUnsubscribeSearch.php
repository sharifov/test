<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\EmailUnsubscribe;

/**
 * EmailUnsubscribeSearch represents the model behind the search form of `common\models\EmailUnsubscribe`.
 */
class EmailUnsubscribeSearch extends EmailUnsubscribe
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eu_email'], 'safe'],
            [['eu_project_id', 'eu_created_user_id'], 'integer'],
            [['eu_created_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = EmailUnsubscribe::find();

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
            'eu_project_id' => $this->eu_project_id,
            'eu_created_user_id' => $this->eu_created_user_id,
            'DATE(eu_created_dt)' => $this->eu_created_dt,
        ]);

        $query->andFilterWhere(['like', 'eu_email', $this->eu_email]);

        return $dataProvider;
    }
}
