<?php

namespace src\model\clientChatRequest\entity\search;

use src\yii\data\BigActiveDataProvider;
use yii\data\ActiveDataProvider;
use src\model\clientChatRequest\entity\ClientChatRequest;

class ClientChatRequestSearch extends ClientChatRequest
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
            ['ccr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ccr_event', 'safe'],

            [['ccr_id','nextId','prevId','cursor'], 'integer'],

            [['ccr_json_data'], 'safe'],

            [['ccr_rid'], 'string', 'max' => 150],
            [['ccr_visitor_id'], 'string', 'max' => 100],
        ];
    }

    public function search($params): BigActiveDataProvider
    {
        $query = static::find()->orderBy(['ccr_id' => SORT_DESC])->distinct();

        $dataProvider = new BigActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'ccr_id' => [
                    'asc' => ['ccr_id' => SORT_ASC],
                    'desc' => ['ccr_id' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
            'defaultOrder' => [
                'ccr_id' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cursor == 1) {
            $this->nextId = null;
        } else if ($this->cursor == 2) {
            $this->prevId = null;
        }

        if ($this->nextId) {
            $query->andFilterWhere(['<', 'ccr_id', $this->nextId]);
        }
        if ($this->prevId) {
            $query->andFilterWhere(['<', 'ccr_id', $this->prevId]);
        }

        $query->andFilterWhere([
            'ccr_id' => $this->ccr_id,
            'ccr_rid' => $this->ccr_rid,
            'ccr_visitor_id' => $this->ccr_visitor_id,
            'DATE(ccr_created_dt)' => $this->ccr_created_dt,
        ]);

        $query->andFilterWhere(['ccr_event' => $this->ccr_event]);

        $query->andFilterWhere(['like', 'ccr_json_data', $this->ccr_json_data]);

        $tableIdColName = 'ccr_id';
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
            $this->filterCount = ClientChatRequest::find()->andFilterWhere($filters)->count();
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

        $prevLimit = ClientChatRequest::getPrevModels($lastId[$tableIdColName], $limit, $filters);

        if (isset($prevLimit) && count($prevLimit) >= $limit) {
            $this->prevId = $lastId[$tableIdColName];
            if (count($prevLimit) > $limit) {
                array_pop($prevLimit);
            }
        }

        //recharge dataprovider when prev
        if ($this->cursor == 1  && $this->prevId != null) {
            $dataProvider->setModels($prevLimit);
            $models = $dataProvider->getModels();
            $modelKeys = $dataProvider->prepareKeys($models);

            $dataProvider->setKeys($modelKeys);
            $lastId = array_shift($modelKeys);

            $prevModels = null;
            if ($lastId) {
                $prevModels = static::getPrevModels($lastId, $limit, $filters);
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
