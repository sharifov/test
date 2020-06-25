<?php

namespace sales\repositories\project;

use common\models\Project;
use sales\repositories\Repository;

class ProjectRepository extends Repository
{
	public function findByName(string $name)
	{
		return Project::find()->select(['id'])->byName($name)->active()->one();
	}

	public function getIdByName(string $name): ?int
	{
		$project = $this->findByName($name);
		if ($project) {
			return $project->id;
		}
		return null;
	}
}