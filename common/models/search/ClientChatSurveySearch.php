<?php

namespace common\models\search;

use common\models\ClientChatSurvey;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class ClientChatSurveySearch
 * @package common\models\search
 */
class ClientChatSurveySearch extends ClientChatSurvey
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ccs_id', 'ccs_uid', 'ccs_chat_id', 'ccs_type', 'ccs_template', 'ccs_trigger_source', 'ccs_requested_by', 'ccs_requested_for', 'ccs_status', 'ccs_created_dt'], 'string'],
            [['ccs_id', 'ccs_uid', 'ccs_chat_id', 'ccs_type', 'ccs_template', 'ccs_trigger_source', 'ccs_requested_by', 'ccs_requested_for', 'ccs_status', 'ccs_created_dt'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ClientChatSurvey::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccs_id' => $this->ccs_id,
            'ccs_requested_by' => $this->ccs_requested_by,
            'ccs_requested_for' => $this->ccs_requested_for,
            'ccs_status' => $this->ccs_status
        ]);

        $query
            ->andFilterWhere(['like', 'ccs_uid', $this->ccs_uid])
            ->andFilterWhere(['like', 'ccs_chat_id', $this->ccs_chat_id])
            ->andFilterWhere(['like', 'ccs_type', $this->ccs_type])
            ->andFilterWhere(['like', 'ccs_template', $this->ccs_template])
            ->andFilterWhere(['like', 'ccs_trigger_source', $this->ccs_trigger_source]);

        /*
         * If `ccs_created_dt` is not empty - we add filtering by this field.
         *
         * I could use something like `$query->andFilterWhere(['DATE(ccs_created_dt)' => $this->ccs_created_dt])`
         * but didn't, because in MySQL the 'DATE/1' function calculates date for every single record in database
         * (including values that not included in the selection), as result - uses more system resources.
         */
        if ($this->ccs_created_dt !== null && $this->ccs_created_dt !== "") {
            $createdDateTime = \DateTime::createFromFormat('Y-m-d', $this->ccs_created_dt);
            $fromCreatedDateTime = "{$createdDateTime->format('Y-m-d')} 00:00:00";
            $toCreatedDateTime = "{$createdDateTime->format('Y-m-d')} 23:59:59";
            $query->andFilterWhere(['BETWEEN', 'ccs_created_dt', $fromCreatedDateTime, $toCreatedDateTime]);
        }

        return $dataProvider;
    }
}
