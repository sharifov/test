<?php

namespace frontend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Employee;
use frontend\models\Log;

/**
 * LogSearch represents the model behind the search form about `frontend\models\Log`.
 */
class LogSearch extends Log
{
    public $days;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level'], 'integer'],
            [['category', 'prefix', 'message', 'log_time'], 'safe'],
            [['days'], 'integer', 'min' => 0, 'max' => 365]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Log::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
        ]);
        if (empty($this->log_time) === false) {
            $from = Employee::convertTimeFromUserDtToUTC(strtotime($this->log_time));
            $from = strtotime($from);
            $to = strtotime(date('Y-m-d 23:59:59', strtotime($this->log_time)));
            $to = strtotime(Employee::convertTimeFromUserDtToUTC($to));
            $query->andOnCondition('log_time >= :from AND log_time <= :to', array(':from' => $from, ':to' => $to));
        }
        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message]);
        $query->orderBy(['id' => SORT_DESC]);
        return $dataProvider;
    }
}
