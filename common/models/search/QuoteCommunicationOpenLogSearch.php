<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteCommunicationOpenLog;

/**
 * Class QuoteCommunicationOpenLogSearch
 * @package common\models\search
 */
class QuoteCommunicationOpenLogSearch extends QuoteCommunicationOpenLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qcol_id', 'qcol_quote_communication_id'], 'integer'],
            [['qcol_id', 'qcol_quote_communication_id', 'qcol_created_dt'], 'safe'],
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
        $query = QuoteCommunicationOpenLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'qcol_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qcol_id' => $this->qcol_id,
            'qcol_quote_communication_id' => $this->qcol_quote_communication_id
        ]);

        /*
         * If `qcol_created_dt` is not empty - we add filtering by this field.
         *
         * I could use something like `$query->andFilterWhere(['DATE(qcol_created_dt)' => $this->qcol_created_dt])`
         * but didn't, because in MySQL the 'DATE/1' function calculates date for every single record in database
         * (including values that not included in the selection), as result - uses more system resources.
         */
        if ($this->qcol_created_dt !== null && $this->qcol_created_dt !== "") {
            $qcCreatedDateTime = \DateTime::createFromFormat('Y-m-d', $this->qcol_created_dt);
            $qcFromCreatedDateTime = "{$qcCreatedDateTime->format('Y-m-d')} 00:00:00";
            $qcToCreatedDateTime = "{$qcCreatedDateTime->format('Y-m-d')} 23:59:59";
            $query->andFilterWhere(['BETWEEN', 'qcol_created_dt', $qcFromCreatedDateTime, $qcToCreatedDateTime]);
        }

        return $dataProvider;
    }
}
