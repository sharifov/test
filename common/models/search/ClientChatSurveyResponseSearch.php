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
            [['ccsr_question', 'ccsr_response'], 'string'],
            [['ccsr_id', 'ccsr_client_chat_survey_id', 'ccsr_question', 'ccsr_response'], 'safe']
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

        return $dataProvider;
    }
}
