<?php

namespace sales\rbac\rules;

use sales\repositories\lead\LeadRepository;
use yii\rbac\Rule;

/**
 * Class LeadOwnerRule
 * @param LeadRepository $leadRepository
 */
class LeadOwnerRule extends Rule
{
    public $name = 'isLeadOwner';

    private $leadRepository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->leadRepository = new LeadRepository();
    }

    public function execute($user, $item, $params): bool
    {
        $id = (int)\Yii::$app->request->post('id');
        if (!$id) {
            $id = (int) \Yii::$app->request->get('id');
        }

        if (!$id && isset($params['id'])) {
            $id = (int)$params['id'];
        }
        try {
            return $this->leadRepository->get($id)->isOwner($user);
        } catch (\Throwable $e) {}
        return false;
    }

}