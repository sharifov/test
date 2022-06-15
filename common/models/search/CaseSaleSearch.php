<?php

namespace common\models\search;

use common\models\Employee;
use src\yii\data\BigActiveDataProvider;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CaseSale;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * CaseSaleSearch represents the model behind the search form of `common\models\CaseSale`.
 */
class CaseSaleSearch extends CaseSale
{
    /**
     * @var mixed|null
     */
    public $reset;
    public $nextId;
    public $prevId;
    public $lastPage;
    public $cursor;
    public $filterCount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['css_cs_id', 'css_sale_id', 'css_sale_pax', 'css_created_user_id', 'css_updated_user_id'], 'integer'],
            [['css_sale_book_id', 'css_sale_pnr', 'css_sale_data','nextId','prevId','cursor'], 'safe'],
            [['css_sale_created_dt', 'css_created_dt', 'css_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            [['css_need_sync_bo'], 'boolean'],
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
        $query = CaseSale::find()->orderBy(['css_cs_id' => SORT_DESC]);
        // add conditions that should always apply here

        $dataProvider = new BigActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'css_cs_id' => [
                    'asc' => ['css_cs_id' => SORT_ASC],
                    'desc' => ['css_cs_id' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
            'defaultOrder' => [
                'css_cs_id' => SORT_DESC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cursor == 1) {
            $this->nextId = null;
        } else if ($this->cursor == 2) {
            $this->prevId = null;
        }

        if ($this->nextId) {
            $query->andFilterWhere(['<', 'css_cs_id', $this->nextId]);
        }
        if ($this->prevId) {
            $query->andFilterWhere(['<', 'css_cs_id', $this->prevId]);
        }

        if ($this->css_sale_created_dt) {
            $query->andFilterWhere(['>=', 'css_sale_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_sale_created_dt))])
                ->andFilterWhere(['<=', 'css_sale_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_sale_created_dt) + 3600 * 24)]);
        }

        if ($this->css_created_dt) {
            $query->andFilterWhere(['>=', 'css_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_created_dt))])
                ->andFilterWhere(['<=', 'css_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_created_dt) + 3600 * 24)]);
        }

        if ($this->css_updated_dt) {
            $query->andFilterWhere(['>=', 'css_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_updated_dt))])
                ->andFilterWhere(['<=', 'css_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->css_updated_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'css_cs_id' => $this->css_cs_id,
            'css_sale_id' => $this->css_sale_id,
            'css_sale_pax' => $this->css_sale_pax,
            //'css_sale_created_dt' => $this->css_sale_created_dt,
            'css_created_user_id' => $this->css_created_user_id,
            'css_updated_user_id' => $this->css_updated_user_id,
            //'css_created_dt' => $this->css_created_dt,
            //'css_updated_dt' => $this->css_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'css_sale_book_id', $this->css_sale_book_id])
            ->andFilterWhere(['like', 'css_sale_pnr', $this->css_sale_pnr]);

        if (ArrayHelper::isIn($this->css_need_sync_bo, ['1', '0'], false)) {
            $query->andWhere(['css_need_sync_bo' => $this->css_need_sync_bo]);
        }

        $tableIdColName = 'css_cs_id';
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
            $this->filterCount = CaseSale::find()->andFilterWhere($filters)->count();
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

        $prevLimit = CaseSale::getPrevModels($lastId[$tableIdColName], $limit, $filters);

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
                $prevModels = CaseSale::getPrevModels($lastId[$tableIdColName], $limit, $filters);
            }

            $this->prevId = $prevModels ? $lastId[$tableIdColName] : null;

            $next = $dataProvider->models;
            $next = array_pop($next);
            $this->nextId = $next[$tableIdColName];
        }

        $this->reset = true;

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchByCase($params)
    {
        $query = CaseSale::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['css_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['css_cs_id' => $this->css_cs_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'css_sale_id' => $this->css_sale_id,
            'css_sale_pax' => $this->css_sale_pax,
            'css_sale_created_dt' => $this->css_sale_created_dt,
            'css_created_user_id' => $this->css_created_user_id,
            'css_updated_user_id' => $this->css_updated_user_id,
            'css_created_dt' => $this->css_created_dt,
            'css_updated_dt' => $this->css_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'css_sale_book_id', $this->css_sale_book_id])
            ->andFilterWhere(['like', 'css_sale_pnr', $this->css_sale_pnr]);

        return $dataProvider;
    }

    public function searchForExport($params)
    {
        $query = CaseSale::find()->where(new Expression('css_send_email_dt is not null'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['css_created_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere(['css_cs_id' => $this->css_cs_id]);

        return $dataProvider;
    }
}
