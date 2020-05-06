<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ClientEmail;
use yii\helpers\VarDumper;

/**
 * ClientEmailSearch represents the model behind the search form of `common\models\ClientEmail`.
 */
class ClientEmailSearch extends ClientEmail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'type'], 'integer'],
            [['email', 'created', 'updated', 'comments'], 'safe'],
            ['ce_title', 'string', 'max' => 150],
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
        $query = ClientEmail::find();

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

        if ($this->type >= 0) {
            $query->andFilterWhere(['=', 'type', $this->type]);
        }

        if ($this->created) {
            $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created))])
                ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($this->created) + 3600 * 24)]);
        }

        if ($this->updated) {
            $query->andFilterWhere(['>=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated))])
                ->andFilterWhere(['<=', 'updated', Employee::convertTimeFromUserDtToUTC(strtotime($this->updated) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'ce_title', $this->ce_title]);

        return $dataProvider;
    }
}
