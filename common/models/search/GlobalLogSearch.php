<?php

namespace common\models\search;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use common\models\Quote;
use src\entities\cases\Cases;
use src\yii\data\BigActiveDataProvider;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GlobalLog;
use yii\db\Query;

/**
 * GlobalLogSearch represents the model behind the search form of `common\models\GlobalLog`.
 *
 * @property int $leadId
 */
class GlobalLogSearch extends GlobalLog
{
    public $leadId;
    public $caseId;

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
            [['gl_id', 'gl_app_user_id', 'gl_obj_id', 'leadId', 'caseId', 'gl_action_type','nextId','prevId','cursor'], 'integer'],
            [['gl_app_id', 'gl_model', 'gl_old_attr', 'gl_new_attr'], 'safe'],
            [['gl_created_at'], 'date', 'format' => 'php:Y-m-d'],
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
     * @return BigActiveDataProvider
     */
    public function search($params)
    {
        $query = GlobalLog::find()->orderBy(['gl_id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new BigActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'gl_id' => [
                    'asc' => ['gl_id' => SORT_ASC],
                    'desc' => ['gl_id' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
            'defaultOrder' => [
                'gl_id' => SORT_DESC
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
            $query->andFilterWhere(['<', 'gl_id', $this->nextId]);
        }
        if ($this->prevId) {
            $query->andFilterWhere(['<', 'gl_id', $this->prevId]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'gl_id' => $this->gl_id,
            'gl_app_user_id' => $this->gl_app_user_id,
            'gl_obj_id' => $this->gl_obj_id,
            'DATE(gl_created_at)' => $this->gl_created_at,
            'gl_model' => $this->gl_model,
            'gl_app_id' => $this->gl_app_id,
            'gl_action_type' => $this->gl_action_type
        ]);

        $tableIdColName = 'gl_id';
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
            $this->filterCount = GlobalLog::find()->andFilterWhere($filters)->count();
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

        $prevLimit = GlobalLog::getPrevModels($lastId[$tableIdColName], $limit, $filters);

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
                $prevModels = GlobalLog::getPrevModels($lastId, $limit, $filters);
            }

            $this->prevId = $prevModels ? $lastId : null;

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
    public function searchByLead($params): ActiveDataProvider
    {
        $query = self::find()->select('*');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $queryLead = GlobalLog::find()->alias('gl')
            ->where(['gl_obj_id' => $this->leadId])
            ->andWhere(['gl_model' => Lead::class]);

        $queryQuote = GlobalLog::find()->alias('gl')
            ->innerJoin('quotes AS q', 'gl.gl_obj_id = q.id')
            ->where(['q.lead_id' => $this->leadId,  'gl.gl_model' => Quote::class]);

        $queryLeadPreferences = GlobalLog::find()->alias('gl')
            ->innerJoin('lead_preferences AS lp', 'gl.gl_obj_id = lp.id')
            ->where(['lp.lead_id' => $this->leadId,  'gl.gl_model' => LeadPreferences::class]);

        $queryLeadFlightSegments = GlobalLog::find()->alias('gl')
            ->innerJoin('lead_flight_segments AS lfp', 'gl.gl_obj_id = lfp.id')
            ->where(['lfp.lead_id' => $this->leadId,  'gl.gl_model' => LeadFlightSegment::class]);

        $queryClientPhone = GlobalLog::find()->alias('gl')
            ->innerJoin('client_phone as cp', 'cp.id = gl.gl_obj_id')
            ->innerJoin('clients as client', 'client.id = cp.client_id')
            ->innerJoin('leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelPhone', [':leadId' => $this->leadId, ':glModelPhone' => ClientPhone::class]);

        $queryClientEmail = GlobalLog::find()->alias('gl')
            ->join('join', 'client_email as ce', 'ce.id = gl.gl_obj_id')
            ->join('join', 'clients as client', 'client.id = ce.client_id')
            ->join('join', 'leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelEmail', [':leadId' => $this->leadId, ':glModelEmail' => ClientEmail::class]);

        $queryClient = GlobalLog::find()->alias('gl')
            ->join('join', 'clients as client', 'client.id = gl.gl_obj_id')
            ->join('join', 'leads as l', 'l.client_id = client.id and l.id = :leadId and gl.gl_model = :glModelClient', [':leadId' => $this->leadId, ':glModelClient' => Client::class]);

        $query->from(['tbl' => $queryLead->union($queryQuote)
                                        ->union($queryLeadPreferences)
                                        ->union($queryLeadFlightSegments)
                                        ->union($queryClientPhone)
                                        ->union($queryClientEmail)
                                        ->union($queryClient)])
            ->orderBy(['gl_id' => SORT_ASC]);


        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchByCase($params): ActiveDataProvider
    {
        $this->load($params);
        $query = GlobalLog::find()->alias('gl');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->innerJoin('cases', 'cases.cs_id = gl.gl_obj_id')
            ->where(['gl_obj_id' => $this->caseId])
            ->andWhere(['gl_model' => GlobalLog::MODEL_CASES])
            ->orderBy(['gl_id' => SORT_ASC]);

        return $dataProvider;
    }
}
