<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserGroup;

/**
 * UserGroupSearch represents the model behind the search form of `common\models\UserGroup`.
 */
class UserGroupSearch extends UserGroup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ug_id', 'ug_disable'], 'integer'],
            [['ug_key', 'ug_name', 'ug_description', 'ug_updated_dt'], 'safe'],
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
        $query = UserGroup::find();

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
            'ug_id' => $this->ug_id,
            'ug_disable' => $this->ug_disable,
            'ug_updated_dt' => $this->ug_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ug_key', $this->ug_key])
            ->andFilterWhere(['like', 'ug_name', $this->ug_name])
            ->andFilterWhere(['like', 'ug_description', $this->ug_description]);

        return $dataProvider;
    }
}
