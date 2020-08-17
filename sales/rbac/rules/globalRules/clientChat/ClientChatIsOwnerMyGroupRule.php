<?php

namespace sales\rbac\rules\globalRules\clientChat;

use common\models\Employee;
use sales\access\EmployeeGroupAccess;
use sales\model\clientChat\entity\ClientChat;

class ClientChatIsOwnerMyGroupRule extends \yii\rbac\Rule
{
	public $name = 'ClientChatIsOwnerMyGroupRule';

	private array $primaryRoles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
		Employee::ROLE_QA,
		Employee::ROLE_QA_SUPER
	];

	private array $roles = [
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_EX_SUPER,
	];

	public function execute($user, $item, $params)
	{
		if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
			return false;
		}
		$chat = $params['chat'];

		if ($currentUser = Employee::findOne((int)$user)) {
			if ($currentUser->canRoles($this->primaryRoles)) {
				return true;
			}
			return ($currentUser->canRoles($this->roles) && EmployeeGroupAccess::isUserInCommonGroup($user, (int)$chat->cch_owner_user_id));
		}
		return false;
	}
}