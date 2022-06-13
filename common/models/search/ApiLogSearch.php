<?php

namespace common\models\search;

use common\models\Employee;
use src\helpers\DateHelper;
use src\yii\data\BigActiveDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApiLog;
use yii\db\Expression;

/**
 * ApiLogSearch represents the model behind the search form of `common\models\ApiLog`.
 *
 * @property string $createTimeRange
 * @property string $createTimeStart
 * @property string $createTimeEnd
 */
class ApiLogSearch extends ApiLog
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $reset;
    public $nextId;
    public $prevId;
    public $lastPage;
    public $cursor;
    public $filterCount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['al_id', 'al_user_id', 'al_memory_usage', 'al_db_query_count'], 'integer'],
            [
                [
                    'al_request_data',
                    'al_response_data',
                    'al_response_dt',
                    'al_ip_address',
                    'al_action',
                    'al_execution_time',
                    'al_db_execution_time',
                    'nextId',
                    'prevId',
                    'cursor'
                ],
                'safe'
            ],
            ['createTimeRange', 'convertDateTimeRange']
        ];
    }

    public function convertDateTimeRange($attribute)
    {
        if ($this->createTimeRange) {
            $date = explode(' - ', $this->createTimeRange);
            if (count($date) === 2) {
                if (DateHelper::checkDateTime($date[0], 'Y-m-d H:i')) {
                    $this->createTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($date[0]));
                } else {
                    $this->addError($attribute, 'CreateTimeStart incorrect format');
                    $this->createTimeRange = null;
                }
                if (DateHelper::checkDateTime($date[1], 'Y-m-d H:i')) {
                    $this->createTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($date[1]));
                } else {
                    $this->addError($attribute, 'CreateTimeEnd incorrect format');
                    $this->createTimeRange = null;
                }
            } else {
                $this->addError($attribute, 'CreateTimeRange is not parsed correctly');
                $this->createTimeRange = null;
            }
        }
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
        $query = ApiLog::find()->orderBy(['al_id' => SORT_DESC]);

        $dataProvider = new BigActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'al_id' => [
                    'asc' => ['al_id' => SORT_ASC],
                    'desc' => ['al_id' => SORT_DESC],
                    'default' => SORT_DESC,
                ],
            ],
            'defaultOrder' => [
                'al_id' => SORT_DESC
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
            $query->andFilterWhere(['<', 'al_id', $this->nextId]);
        }
        if ($this->prevId) {
            $query->andFilterWhere(['<', 'al_id', $this->prevId]);
        }

        $query->andFilterWhere([
            'al_id' => $this->al_id,
            'al_response_dt' => $this->al_response_dt,
            'al_user_id' => $this->al_user_id,
            'al_memory_usage' => $this->al_memory_usage,
            'al_db_query_count' => $this->al_db_query_count,
            'al_action' => $this->al_action
        ]);

        if ($this->createTimeStart && $this->createTimeEnd) {
            $query->andWhere(['BETWEEN', 'al_request_dt', $this->createTimeStart, $this->createTimeEnd]);
        }

        $query->andFilterWhere(['like', 'al_request_data', $this->al_request_data])
            ->andFilterWhere(['like', 'al_response_data', $this->al_response_data])
            ->andFilterWhere(['like', 'al_ip_address', $this->al_ip_address]);

        $tableIdColName = 'al_id';
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
            $this->filterCount = ApiLog::find()->andFilterWhere($filters)->count();
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

        $prevLimit = ApiLog::getPrevModels($lastId[$tableIdColName], $limit, $filters);

        if (isset($prevLimit) && count($prevLimit) >= $limit) {
            $this->prevId = $lastId[$tableIdColName];
            if (count($prevLimit) > $limit) {
                array_pop($prevLimit);
            }
        }

        //recharge dataprovider when prev
        if ($this->cursor == 1) {
            $dataProvider->setModels($prevLimit);
            $models = $dataProvider->getModels();
            $modelKeys = $dataProvider->prepareKeys($models);

            $dataProvider->setKeys($modelKeys);
            $lastId = array_shift($modelKeys);

            $prevModels = null;
            if ($lastId) {
                $prevModels = ApiLog::getPrevModels($lastId, $limit, $filters);
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
     * @param string $fromDate
     * @param string $todate
     * @param string $range
     * @param string $apiUserId
     * @param string $selectedAction
     * @return array
     */
    public static function getApiLogStats(
        string $fromDate,
        string $todate,
        string $range,
        string $apiUserId,
        string $selectedAction
    ): array {
        if ($range == 'H') {
            $queryDateFormat = 'HH24:00';
        } elseif ($range == 'D') {
            $queryDateFormat = 'YYYY-MM-DD';
        } elseif ($range == 'M') {
            $queryDateFormat = 'YYYY-MON';
        } elseif ($range == 'HD') {
            $queryDateFormat = 'YYYY-MM-DD HH24:00';
        }

        $actionList = self::getActionsList();

        $apiStatsQuery = self::find();
        $apiStatsQuery->select(["al_action, AVG(al_execution_time) AS execution_time, AVG(al_memory_usage) AS memUsage, DATE(al_request_dt) as create_date, to_char(al_request_dt, '$queryDateFormat' ) as timeLine, COUNT(*) AS cnt"]);
        $apiStatsQuery->from('api_log');
        $apiStatsQuery->where(['between', 'DATE(al_request_dt)', $fromDate, $todate]);
        $apiStatsQuery->andWhere('al_execution_time IS NOT NULL');
        if ($apiUserId != '') {
            $apiStatsQuery->andWhere(['=', 'al_user_id', $apiUserId]);
        }
        if ($selectedAction != '') {
            $apiStatsQuery->andWhere(['=', 'al_action', $selectedAction]);
        }
        $apiStatsQuery->groupBy(["al_action, create_date, to_char(al_request_dt, '$queryDateFormat')"]);
        $apiStatsQuery->orderBy(new Expression('create_date ASC, execution_time DESC, timeLine ASC'));
        $result = $apiStatsQuery->asArray()->all();
        $apiStats = [];

        foreach ($actionList as $actionKey => $action) {
            foreach ($result as $key => $item) {
                if ($action['al_action'] == $item['al_action']) {
                    $apiStats[$item['timeline']]['action' . $actionKey] = $item['al_action'];
                    $apiStats[$item['timeline']]['exetime' . $actionKey] = $item['execution_time'];
                    $apiStats[$item['timeline']]['memusage' . $actionKey] = $item['memusage'];
                    $apiStats[$item['timeline']]['cnt' . $actionKey] = $item['cnt'];
                    $apiStats[$item['timeline']]['timeline'] = $item['timeline'];
                }
            }
        }

        ksort($apiStats);
        return $apiStats;
    }
}
