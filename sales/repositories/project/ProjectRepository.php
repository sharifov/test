<?php

namespace sales\repositories\project;

use common\models\Project;
use sales\repositories\NotFoundException;

class ProjectRepository
{
    public function findByName(string $name)
    {
        return Project::find()->select(['id'])->byName($name)->active()->one();
    }

    public function findByKey(string $key)
    {
        if ($project = Project::find()->select(['id'])->byKey($key)->active()->one()) {
            return $project;
        }
        \Yii::error('Project not found by key: ' . $key, 'ProjectRepository::findByKey::notFound');
        throw new NotFoundException('Project not found by key: ' . $key);
    }

    public function findByApiKey(string $key)
    {
        if ($project = Project::find()->select(['id'])->byApiKey($key)->active()->one()) {
            return $project;
        }
        \Yii::error('Project not found by key: ' . $key, 'ProjectRepository::findByKey::notFound');
        throw new NotFoundException('Project not found by key: ' . $key);
    }

    public function getIdByName(string $name): ?int
    {
        $project = $this->findByName($name);
        return $project->id ?? null;
    }

    public function getIdByProjectKey(string $key): ?int
    {
        $project = $this->findByKey($key);
        return $project->id ?? null;
    }

    public function findById(int $id): Project
    {
        if ($project = Project::findOne(['id' => $id])) {
            return $project;
        }
        throw new NotFoundException('Project not found by id: ' . $id);
    }
}
