<?php

namespace sales\rbac\rules;

use sales\repositories\lead\LeadRepository;
use yii\rbac\Rule;

abstract class LeadRule extends Rule
{
    protected $leadRepository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->leadRepository = new LeadRepository();
    }

    abstract public function execute($user, $item, $params);
}