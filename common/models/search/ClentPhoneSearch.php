<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ClientPhone;
use yii\helpers\VarDumper;

/**
 * ClentPhoneSearch represents the model behind the search form of `common\models\ClientPhone`.
 */
class ClentPhoneSearch extends ClientPhone
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'is_sms'], 'integer'],
            [['phone', 'created', 'updated', 'comments', 'validate_dt'], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */ /*
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }*/

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ClientPhone::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        //VarDumper::dump($dataProvider, 10, true); exit;
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'created' => $this->created,
            //'updated' => $this->updated,
            'is_sms' => $this->is_sms,
            'validate_dt' => $this->validate_dt,
        ]);

        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'comments', $this->comments]);
        //VarDumper::dump($query, 10, true); exit;
        return $dataProvider;
    }
}
