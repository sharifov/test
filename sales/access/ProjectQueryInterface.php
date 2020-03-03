<?php

namespace sales\access;

use yii\db\ActiveQuery;

interface ProjectQueryInterface
{
    public function projects(array $projects);
}
