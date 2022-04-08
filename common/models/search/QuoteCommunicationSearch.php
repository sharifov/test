<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\QuoteCommunication;

/**
 * Class QuoteCommunicationSearch
 * @package common\models\search
 */
class QuoteCommunicationSearch extends QuoteCommunication
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qc_communication_id', 'qc_communication_type', 'qc_quote_id', 'qc_created_by'], 'integer'],
            [['qc_created_dt'], 'safe'],
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
        $query = QuoteCommunication::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'qc_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qc_id' => $this->qc_id,
            'qc_communication_type' => $this->qc_communication_type,
            'qc_communication_id' => $this->qc_communication_id,
            'qc_quote_id' => $this->qc_quote_id,
            'qc_created_by' => $this->qc_created_by
        ]);

        /*
         * If `qc_created_dt` is not empty - we add filtering by this field.
         *
         * I could use something like `$query->andFilterWhere(['DATE(qc_created_dt)' => $this->qc_created_dt])`
         * but didn't, because in MySQL the 'DATE/1' function calculates date for every single record in database
         * (including values that not included in the selection), as result - uses more system resources.
         */
        if ($this->qc_created_dt !== null) {
            $qcCreatedDateTime = \DateTime::createFromFormat('Y-m-d', $this->qc_created_dt);
            $qcFromCreatedDateTime = "{$qcCreatedDateTime->format('Y-m-d')} 00:00:00";
            $qcToCreatedDateTime = "{$qcCreatedDateTime->format('Y-m-d')} 23:59:59";
            $query->andFilterWhere(['BETWEEN', 'qc_created_dt', $qcFromCreatedDateTime, $qcToCreatedDateTime]);
        }

        return $dataProvider;
    }
}
