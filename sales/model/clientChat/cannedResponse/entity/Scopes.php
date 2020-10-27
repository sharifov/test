<?php

namespace sales\model\clientChat\cannedResponse\entity;

use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use yii\db\Expression;

/**
 * @see \sales\model\clientChat\clientChatCannedResponse\entity\ClientChatCannedResponse
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byProjectId(int $id): Scopes
    {
        return $this->andWhere(['cr_project_id' => $id]);
    }

    public function byTsVectorMessage(string $searchSubString): Scopes
    {
        return $this->andWhere(new Expression("to_tsvector('english', cr_message) @@ to_tsquery('english', :message)"), ['message' => $searchSubString]);
    }

    public function joinCategory(): Scopes
    {
        return $this->innerJoin(ClientChatCannedResponseCategory::tableName(), 'cr_category_id = crc_id');
    }

    public function categoryEnabled(): Scopes
    {
        return $this->andWhere(['crc_enabled' => true]);
    }

    public function byLanguageId(string $id): Scopes
    {
        return $this->andWhere(['cr_language_id' => $id]);
    }
}
