<?php

namespace sales\rbac\rules;

use sales\repositories\lead\LeadRepository;
use yii\rbac\Rule;

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

    public function execute($user, $item, $params): bool
    {
        $id = (int)\Yii::$app->request->post('id');
        if (!$id && isset($params['id'])) {
            $id = (int)$params['id'];
        }
        try {
            $lead = $this->leadRepository->get($id);
            $userGroups = array_keys(\Yii::$app->user->identity->userGroupList);
            $employeeGroups = array_keys($lead->employee->userGroupList);
            foreach ($userGroups as $group) {
                if (in_array($group, $employeeGroups)) {
                    return true;
                }
            }
        } catch (\Throwable $e) {}
        return false;
    }

}