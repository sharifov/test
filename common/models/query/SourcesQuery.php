<?php

namespace common\models\query;

use common\models\Project;
use common\models\Sources;

/**
 * This is the ActiveQuery class for [[Sources]].
 *
 * @see Sources
 */
class SourcesQuery extends \yii\db\ActiveQuery
{

    public function byCid(string $cid): self
    {
        return $this->andWhere(['cid' => $cid]);
    }

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['hidden' => false]);
    }

    public static function getDefaultSourceByProjectId(int $id): ?Sources
    {
        return (Sources::findOne(['default' => 1, 'project_id' => $id]));
    }

    public static function getFirstSourceByProjectId(int $id): ?Sources
    {
        return Sources::findOne(['project_id' => $id]);
    }

    public static function getByCidOrDefaultByProject(string $cid, int $projectId): ?Sources
    {
        if ($source = Sources::find()->byCid($cid)->one()) {
            return $source;
        }
        return self::getDefaultSourceByProjectId($projectId);
    }
}
