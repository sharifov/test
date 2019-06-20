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

    public function execute($user, $item, $params): bool
    {
        $id = (int)Yii::$app->request->post('id');
        if (!$id && isset($params['id'])) {
            $id = (int)$params['id'];
        }
        try {
            $lead = $this->leadRepository->get($id);
            return ($lead->canAgentEdit($user) || $lead->canSupervisionEdit(Yii::$app->user->identity->userGroupList));
        } catch (\Throwable $e) {}
        return false;
    }

}