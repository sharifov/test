<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Project;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;
use yii\helpers\VarDumper;

/**
 * ClientSearch represents the model behind the search form of `common\models\Client`.
 */
class ClientSearch extends Client
{
    public $client_email;
    public $client_phone;
    public $not_in_client_id;

    private int $leadsLimit = 5;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'not_in_client_id'], 'integer'],
            [['client_email', 'client_phone'], 'string'],
            [['first_name', 'middle_name', 'last_name'], 'safe'],
            ['uuid', 'string', 'max' => 36],
            [['company_name'], 'string', 'max' => 150],
            [['is_company', 'is_public', 'disabled'], 'boolean'],

            ['cl_project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['cl_project_id', 'integer'],

            ['parent_id', 'integer'],

            [['created', 'updated'], 'date', 'format' => 'php:Y-m-d'],

            ['cl_excluded', 'boolean'],

            ['cl_type_id', 'integer']
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Client::find()->alias('cl')->joinWith(['project p']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $dataProvider->sort->attributes['cl_project_id'] = [
            'asc' => [Project::tableName() . '.name' => SORT_ASC],
            'desc' => [Project::tableName() . '.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
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
            'cl.id' => $this->id,
        ]);

        if ($this->not_in_client_id) {
            $query->andWhere(['NOT IN', 'cl.id', $this->not_in_client_id]);
        }

        if ($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'cl.id', $subQuery]);
        }

        if ($this->client_phone) {
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'cl.id', $subQuery]);
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        $query->andFilterWhere([
            'is_company' => $this->is_company,
            'is_public' => $this->is_public,
            'disabled' => $this->disabled,
            'parent_id' => $this->parent_id,
            'cl_excluded' => $this->cl_excluded,
        ]);

        if ($this->cl_project_id === -1) {
            $query->andWhere(['IS', 'cl_project_id', null]);
        } else {
            $query->andFilterWhere([
                'cl_project_id' => $this->cl_project_id,
            ]);
        }

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
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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

        if ($this->not_in_client_id) {
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

    public function searchByCallRecording($params): ActiveDataProvider
    {
        $query = static::find()->andWhere(['cl_call_recording_disabled' => true]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageParam' => 'client-page',
                'pageSizeParam' => 'client-per-page',
            ],
            'sort' => [
                'sortParam' => 'client-sort',
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'company_name', $this->company_name]);

        $query->andFilterWhere([
            'is_company' => $this->is_company,
            'is_public' => $this->is_public,
            'disabled' => $this->disabled,
            'cl_excluded' => $this->cl_excluded,
            'cl_type_id' => $this->cl_type_id,
        ]);

        return $dataProvider;
    }

    /**
     * @return int
     */
    public function getLeadsLimit(): int
    {
        return $this->leadsLimit;
    }

    /**
     * @param int $leadsLimit
     */
    public function setLeadsLimit(int $leadsLimit): void
    {
        $this->leadsLimit = $leadsLimit;
    }
}
