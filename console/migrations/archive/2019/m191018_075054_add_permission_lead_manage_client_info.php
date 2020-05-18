<?php

use yii\db\Migration;

/**
 * Class m191018_075054_add_permission_lead_manage_client_info
 */
class m191018_075054_add_permission_lead_manage_client_info extends Migration
{
	/**
	 * @var array
	 */
	public $routes = [
		'/lead-view/ajax-add-client-phone-modal-content',
		'/lead-view/ajax-add-client-phone-validation',
		'/lead-view/ajax-add-client-phone',
		'/lead-view/ajax-edit-client-phone-modal-content',
		'/lead-view/ajax-edit-client-phone-validation',
		'/lead-view/ajax-edit-client-phone',
		'/lead-view/ajax-add-client-email-modal-content',
		'/lead-view/ajax-add-client-email-validation',
		'/lead-view/ajax-add-client-email',
		'/lead-view/ajax-edit-client-email-modal-content',
		'/lead-view/ajax-edit-client-email-validation',
		'/lead-view/ajax-edit-client-email',
		'/lead-view/ajax-edit-client-name-modal-content',
		'/lead-view/ajax-edit-client-name-validation',
		'/lead-view/ajax-edit-client-name',
		'/lead-view/ajax-get-users-same-email-info',
		'/lead-view/ajax-get-users-same-phone-info'
	];

	/**
	 * @var array
	 */
	public $roles = [
		'admin', 'agent', 'supervision', 'ex_agent', 'ex_super'
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{

		/******** RBAC permissions by name ********/
//		$admin = $auth->getRole('admin');
//		$agent = $auth->getRole('agent');
//		$supervisor = $auth->getRole('supervision');
//
//		$leadViewClientPhoneRule = new LeadViewClientPhoneRule();
//		$auth->add($leadViewClientPhoneRule);
//
//		$leadViewClientPhoneCommonGroup = new LeadViewClientPhoneCommonGroup();
//		$auth->add($leadViewClientPhoneCommonGroup);
//
//		$viewLeadClientPhone = $auth->createPermission('leadViewClientPhone');
//		$auth->add($viewLeadClientPhone);
//		$auth->addChild($admin, $viewLeadClientPhone);
//
//		$ownerViewSameUsersByPhone = $auth->createPermission('leadViewClientPhoneOwner');
//		$ownerViewSameUsersByPhone->description = 'Lead View: permission to view users with the same phone number';
//		$ownerViewSameUsersByPhone->ruleName = $leadViewClientPhoneRule->name;
//		$auth->add($ownerViewSameUsersByPhone);
//		$auth->addChild($ownerViewSameUsersByPhone, $viewLeadClientPhone);
//		$auth->addChild($agent, $viewLeadClientPhone);
//
//		$viewLeadClientPhoneCommonGroup = $auth->createPermission('leadViewClientPhoneCommonGroup');
//		$viewLeadClientPhoneCommonGroup->description = 'Lead View: permission to view users with the same phone number';
//		$viewLeadClientPhoneCommonGroup->ruleName = $leadViewClientPhoneCommonGroup->name;
//		$auth->add($viewLeadClientPhoneCommonGroup);
//		$auth->addChild($viewLeadClientPhoneCommonGroup, $viewLeadClientPhone);
//		$auth->addChild($supervisor, $viewLeadClientPhone);
		/********** END ********/


		$auth = Yii::$app->authManager;

		foreach ($this->routes as $route) {

			if (!$permission = $auth->getPermission($route)) {
				$permission = $auth->createPermission($route);
				$auth->add($permission);
			}

			foreach ($this->roles as $role) {
				if (!$auth->hasChild($auth->getRole($role), $permission)) {
					$auth->addChild($auth->getRole($role), $permission);
				}
			}
		}

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$auth = Yii::$app->authManager;

		foreach ($this->routes as $route) {
			foreach ($this->roles as $role) {
				if ($permission = $auth->getPermission($route)) {
					if ($auth->hasChild($auth->getRole($role), $permission)) {
						$auth->removeChild($auth->getRole($role), $permission);
					}
				}
			}
		}

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
