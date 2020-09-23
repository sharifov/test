<?php

namespace sales\repositories\project;

use common\models\Project;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ProjectRepository extends Repository
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

	public function getIdByName(string $name): ?int
	{
		$project = $this->findByName($name);
		return $project->id ?? null;
	}

	public function getIdByProjectKey(string $key): ?int
	{
		$project = $key ? $this->findByKey($key) : null;
		return $project->id ?? null;
	}
}