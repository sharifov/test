<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;

/**
 * ClientSearch represents the model behind the search form of `common\models\Client`.
 */
class ClientSearch extends Client
{

    public $client_email;
    public $client_phone;
    public $not_in_client_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'not_in_client_id'], 'integer'],
            [['client_email', 'client_phone'], 'string'],
            [['first_name', 'middle_name', 'last_name', 'created', 'updated'], 'safe'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Client::find()->with('leads.employee.ugsGroups');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'DATE(created)' => $this->created,
            'DATE(updated)' => $this->updated,
        ]);

        if($this->not_in_client_id) {
            $query->andWhere(['NOT IN', 'id', $this->not_in_client_id]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }

        if ($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchFromLead($params): ActiveDataProvider
    {
        $query = Client::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'DATE(created)' => $this->created,
            'DATE(updated)' => $this->updated,
        ]);

        if($this->not_in_client_id) {
            $query->andWhere(['NOT IN', 'id', $this->not_in_client_id]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['email' => $this->client_email]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }

        if ($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['phone' => $this->client_phone]);
            $query->andWhere(['IN', 'id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

        return $dataProvider;
    }
}
