<?php

namespace common\models\search;

use common\models\Employee;
use sales\helpers\DateHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApiLog;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['al_id', 'al_user_id', 'al_memory_usage', 'al_db_query_count'], 'integer'],
            [['al_request_data', 'al_response_data', 'al_response_dt', 'al_ip_address', 'al_action', 'al_execution_time', 'al_db_execution_time'], 'safe'],
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
                    $this->addError($attribute, 'createTimeEnd incorrect format');
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
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ApiLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['al_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
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
           // ->andFilterWhere(['like', 'al_action', $this->al_action])
            ->andFilterWhere(['like', 'al_ip_address', $this->al_ip_address]);

        return $dataProvider;
    }
}
