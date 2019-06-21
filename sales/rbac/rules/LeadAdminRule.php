<?php

namespace sales\rbac\rules;

use sales\repositories\lead\LeadRepository;
use yii\rbac\Rule;
use Yii;

/**
 * Class LeadOwnerRule
 * @param LeadRepository $leadRepository
 */
class LeadAdminRule extends Rule
{
    public $name = 'isLeadAdmin';

    private $leadRepository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->leadRepository = new LeadRepository();
    }

    public function execute($userId, $item, $params): bool
    {
        $leadId = LeadRequestHelper::getId($params);
        $key = $this->name . '-' . $userId . '-' . $leadId;
        $can = Yii::$app->user->identity->getCache($key);
        if ($can === null) {
            try {
                $can = Yii::$app->user->identity->setCache($key, $this->leadRepository->get($leadId)->canAdminEdit());
            } catch (\Throwable $e) {
                return false;
            }
        }
        return $can;
    }

}