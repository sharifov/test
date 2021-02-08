<?php

namespace sales\model\project\entity\projectLocale;

/**
 * This is the ActiveQuery class for [[ProjectLocale]].
 */
class ProjectLocaleScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ProjectLocale[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProjectLocale|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function enabled(): ProjectLocaleScopes
    {
        return $this->andWhere(['pl_enabled' => true]);
    }

    public function default(): ProjectLocaleScopes
    {
        return $this->andWhere(['pl_default' => true]);
    }

    /**
     * @param int $projectId
     * @return ProjectLocaleScopes
     */
    public function byProject(int $projectId): ProjectLocaleScopes
    {
        return $this->andWhere(['pl_project_id' => $projectId]);
    }

    public function byLanguage(?string $languageId): ProjectLocaleScopes
    {
        return $this->andWhere(['pl_language_id' => $languageId]);
    }

    public function byMarketCountry(?string $marketCountry): ProjectLocaleScopes
    {
        return $this->andWhere(['pl_market_country' => $marketCountry]);
    }

    public function languageNotNull(): ProjectLocaleScopes
    {
        return $this->andWhere(['IS NOT', 'pl_language_id', null]);
    }
}
