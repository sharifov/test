<?php

namespace common\models\search;

use common\models\QuoteUrlActivity;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class QuoteUrlActivitySearch
 * @package common\models\search
 */
class QuoteUrlActivitySearch extends QuoteUrlActivity
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['qua_quote_id', 'qua_communication_type', 'qua_status'], 'integer'],
            [['qua_created_dt', 'qua_ext_data'], 'string'],
            [['qua_id', 'qua_uid', 'qua_quote_id', 'qua_communication_type', 'qua_status', 'qua_ext_data', 'qua_created_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = QuoteUrlActivity::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'qua_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'qua_id' => $this->qua_id,
            'qua_uid' => $this->qua_uid,
            'qua_quote_id' => $this->qua_quote_id,
            'qua_status' => $this->qua_status,
            'qua_communication_type' => $this->qua_communication_type,
            'qua_ext_data' => $this->qua_ext_data
        ]);

        /*
         * If `qua_created_dt` is not empty - we add filtering by this field.
         *
         * I could use something like `$query->andFilterWhere(['DATE(qua_created_dt)' => $this->qua_created_dt])`
         * but didn't, because in MySQL the 'DATE/1' function calculates date for every single record in database
         * (including values that not included in the selection), as result - uses more system resources.
         */
        if ($this->qua_created_dt !== null && $this->qua_created_dt !== "") {
            $quaCreatedDateTime = \DateTime::createFromFormat('Y-m-d', $this->qua_created_dt);
            $quaFromCreatedDateTime = "{$quaCreatedDateTime->format('Y-m-d')} 00:00:00";
            $qcToCreatedDateTime = "{$quaCreatedDateTime->format('Y-m-d')} 23:59:59";
            $query->andFilterWhere(['BETWEEN', 'qua_created_dt', $quaFromCreatedDateTime, $qcToCreatedDateTime]);
        }

        return $dataProvider;
    }
}
