<?php

namespace sales\model\clientChat\cannedResponse\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse;
use yii\db\Expression;

class ClientChatCannedResponseSearch extends ClientChatCannedResponse
{
    public function rules(): array
    {
        return [
            ['cr_category_id', 'integer'],

            [['cr_created_dt', 'cr_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cr_id', 'integer'],

            ['cr_language_id', 'safe'],

            ['cr_message', 'safe'],

            ['cr_project_id', 'integer'],

            ['cr_sort_order', 'integer'],

            ['cr_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cr_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cr_id' => $this->cr_id,
            'cr_project_id' => $this->cr_project_id,
            'cr_category_id' => $this->cr_category_id,
            'cr_user_id' => $this->cr_user_id,
            'cr_sort_order' => $this->cr_sort_order,
            'date(cr_created_dt)' => $this->cr_created_dt,
            'date(cr_updated_dt)' => $this->cr_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cr_language_id', $this->cr_language_id])
            ->andFilterWhere(['like', 'cr_message', $this->cr_message]);

        return $dataProvider;
    }

    public function searchCannedResponse(?int $projectId, string $searchSubString, int $userId, ?string $languageId): array
    {
        $query = self::find()->select(
            (new Expression(
                "ts_headline('english', cr_message, to_tsquery('english', :substring)) as headline_message,
                cr_message as message",
                ['substring' => $searchSubString]
            ))
        );

        $query->byTsVectorMessage($searchSubString)
            ->joinCategory()
            ->categoryEnabled();

        $query->andWhere([
            'OR',
                ['cr_user_id' => $userId],
                ['cr_user_id' => null]
        ]);

        if ($languageId) {
            $query->byLanguageId($languageId);
        }
        if ($projectId) {
            $query->andWhere([
                'OR',
                    ['cr_project_id' => $projectId],
                    ['cr_project_id' => null]
            ]);
        }
        $query->orderBy(['cr_sort_order' => SORT_ASC]);

        return $query->asArray()->all();
    }
}
