<?php

namespace sales\rbac\rules;

use sales\repositories\lead\LeadRepository;
use yii\rbac\Rule;
use Yii;

/**
 * Class LeadSupervisionRule
 * @param LeadRepository $leadRepository
 */
class LeadSupervisionRule extends Rule
{
    public $name = 'isLeadSupervision';

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
                $lead = $this->leadRepository->get($leadId);
                $bool = $lead->canAgentEdit($userId) || $lead->canSupervisionEdit(Yii::$app->user->identity->userGroupList);
                $can = Yii::$app->user->identity->setCache($key, $bool);
            } catch (\Throwable $e) {
                return false;
            }
        }
        return $can;
    }

}