<?php

namespace common\models\search;

use common\models\ClientChatSurveyResponse;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class ClientChatSurveyResponseSearch
 * @package common\models\search
 */
class ClientChatSurveyResponseSearch extends ClientChatSurveyResponse
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['ccsr_id', 'ccsr_client_chat_survey_id'], 'integer'],
            [['ccsr_question', 'ccsr_response', 'ccsr_created_dt'], 'string'],
            [['ccsr_id', 'ccsr_client_chat_survey_id', 'ccsr_question', 'ccsr_response', 'ccsr_created_dt'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $clientChatSurveyId
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchByClientChatSurveyId($clientChatSurveyId, $params)
    {
        $query = ClientChatSurveyResponse::find()
            ->where('ccsr_client_chat_survey_id=:id', [':id' => $clientChatSurveyId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccsr_id' => $this->ccsr_id
        ]);

        $query
            ->andFilterWhere(['like', 'ccsr_question', $this->ccsr_question])
            ->andFilterWhere(['like', 'ccsr_response', $this->ccsr_response]);

        /*
         * If `ccsr_created_dt` is not empty - we add filtering by this field.
         *
         * I could use something like `$query->andFilterWhere(['DATE(ccsr_created_dt)' => $this->ccsr_created_dt])`
         * but didn't, because in MySQL the 'DATE/1' function calculates date for every single record in database
         * (including values that not included in the selection), as result - uses more system resources.
         */
        if ($this->ccsr_created_dt !== null && $this->ccsr_created_dt !== "") {
            $createdDateTime = \DateTime::createFromFormat('Y-m-d', $this->ccsr_created_dt);
            $fromCreatedDateTime = "{$createdDateTime->format('Y-m-d')} 00:00:00";
            $toCreatedDateTime = "{$createdDateTime->format('Y-m-d')} 23:59:59";
            $query->andFilterWhere(['BETWEEN', 'ccsr_created_dt', $fromCreatedDateTime, $toCreatedDateTime]);
        }

        return $dataProvider;
    }
}
