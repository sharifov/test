<?php

namespace src\model\conference\entity\conferenceEventLog\search;

use common\models\Employee;
use src\yii\data\BigActiveDataProvider;
use yii\data\ActiveDataProvider;
use src\model\conference\entity\conferenceEventLog\ConferenceEventLog;

class ConferenceEventLogSearch extends ConferenceEventLog
{
    public $reset;
    public $nextId;
    public $prevId;
    public $lastPage;
    public $cursor;
    public $filterCount;

    public function rules(): array
    {
        return [
            ['cel_conference_sid', 'string'],

            ['cel_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cel_event_type', 'string'],

            [['cel_id', 'nextId','prevId','cursor'], 'integer'],

            ['cel_sequence_number', 'integer'],
        ];
    }

    public function search($params, Employee $user): BigActiveDataProvider
    {
        $query = static::find()->orderBy(['cel_id' => SORT_DESC]);

        $dataProvider = new BigActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'cel_id' => [
                    'asc' => ['cel_id' => SORT_ASC],
                    'desc' => ['cel_id' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
            'defaultOrder' => [
                'cel_id' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cursor == 1) {
            $this->nextId = null;
        } else if ($this->cursor == 2) {
            $this->prevId = null;
        }

        if ($this->nextId) {
            $query->andFilterWhere(['<', 'cel_id', $this->nextId]);
        }
        if ($this->prevId) {
            $query->andFilterWhere(['<', 'cel_id', $this->prevId]);
        }

        if ($this->cel_created_dt) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cel_created_dt', $this->cel_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'cel_id' => $this->cel_id,
            'cel_sequence_number' => $this->cel_sequence_number,
            'cel_event_type' => $this->cel_event_type,
            'cel_conference_sid' => $this->cel_conference_sid,
        ]);

        $tableIdColName = 'cel_id';
        $filters = $query->where;
        if ($filters) {
            foreach ($filters as $filter) {
                if (is_array($filter)) {
                    $key = array_search($tableIdColName, $filter);
                    if ($key) {
                        unset($filters[$key]);
                    }
                }
            }
            if (in_array($tableIdColName, $filters)) {
                $filters = null;
            }
        }

        if (!empty($filters)) {
            $this->filterCount = ConferenceEventLog::find()->andFilterWhere($filters)->count();
        }

        $limit = $dataProvider->pagination->getLimit();

        //Next Button
        if (count($dataProvider->models) > $limit) {
            $models = $dataProvider->models;
            array_pop($models);
            $modelKeys = $dataProvider->prepareKeys($models);

            $dataProvider->setModels($models);
            $dataProvider->setKeys($modelKeys);

            $next = $dataProvider->models;
            $next = array_pop($next);
            $this->nextId = $next[$tableIdColName];
        } else {
            $this->nextId = null;
        }

        //Prev Button
        $newModelCol = array_column($dataProvider->getModels(), $tableIdColName);
        $modelKeys = [];
        foreach ($newModelCol as $value) {
            $modelKeys[][$tableIdColName] = $value;
        }
        $lastId = array_shift($modelKeys);
        $prevLimit = null;
        if (!isset($lastId[$tableIdColName])) {
            $lastId[$tableIdColName] = $this->prevId;
        }

        $prevLimit = ConferenceEventLog::getPrevModels($lastId[$tableIdColName], $limit, $filters);

        if (isset($prevLimit) && count($prevLimit) >= $limit) {
            $this->prevId = $lastId[$tableIdColName];
            if (count($prevLimit) > $limit) {
                array_pop($prevLimit);
            }
        }

        //recharge dataprovider when prev
        if ($this->cursor == 1 && $this->prevId != null) {
            $dataProvider->setModels($prevLimit);
            $models = $dataProvider->getModels();
            $modelKeys = $dataProvider->prepareKeys($models);

            $dataProvider->setKeys($modelKeys);
            $lastId = array_shift($modelKeys);

            $prevModels = null;
            if ($lastId) {
                $prevModels = ConferenceEventLog::getPrevModels($lastId, $limit, $filters);
            }

            $this->prevId = $prevModels ? $lastId : null;

            $next = $dataProvider->models;
            $next = array_pop($next);
            $this->nextId = $next[$tableIdColName];
        }

        $this->reset = true;

        return $dataProvider;
    }
}
