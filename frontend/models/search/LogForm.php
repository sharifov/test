<?php

namespace frontend\models\search;

use common\models\Log;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LogForm extends Log
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level'], 'integer'],
            [['category', 'prefix', 'message', 'log_time'], 'safe'],
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
            $from = strtotime(date('Y-m-d 00:00:00', strtotime($this->log_time)));
            $to = strtotime(date('Y-m-d 23:59:59', strtotime($this->log_time)));
            $query->andOnCondition('log_time >= :from AND log_time <= :to', array(':from' => $from, ':to' => $to));
        }
        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message]);
        $query->orderBy(['id' => SORT_DESC]);
        return $dataProvider;
    }
}
