<?php

namespace sales\model\clientChat\entity;

use common\models\Employee;
use sales\access\EmployeeAccessHelper;
use sales\access\EmployeeGroupAccess;

/**
 * @see ClientChat
 */
class Scopes extends \yii\db\ActiveQuery
{
	public const ROLES_FULL_ACCESS = [
        Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
    ];

    public const ROLES_MANAGERS = [
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_SUP_SUPER,
    ];

	public function byChannel(int $id): self
	{
		return $this->andWhere(['cch_channel_id' => $id]);
	}

	public function byChannelIds(array $ids): self
	{
		return $this->andWhere(['IN', 'cch_channel_id', $ids]);
	}

	public function byIds(array $ids): self
	{
		return $this->andWhere(['IN', 'cch_id', $ids]);
	}

	public function byOwner(int $userId): self
	{
		return $this->andWhere(['cch_owner_user_id' => $userId]);
	}

	public function byRid(string $rid): self
	{
		return $this->andWhere(['cch_rid' => $rid]);
	}

	public function notClosed(): self
	{
		return $this->andWhere(['<>', 'cch_status_id', ClientChat::STATUS_CLOSED]);
	}

	public function active(): self
	{
		return $this->notClosed();
	}

	public function archive(): self
	{
		return $this->andWhere(['cch_status_id' => ClientChat::STATUS_CLOSED]);
	}

	public function byClientId(int $id): self
	{
		return $this->andWhere(['cch_client_id' => $id]);
	}

	public function byUserRestriction(Employee $user): self
    {
        $fullAccess = EmployeeAccessHelper::entryInRoles($user, self::ROLES_FULL_ACCESS);
        $isManager = EmployeeAccessHelper::entryInRoles($user, self::ROLES_MANAGERS);

        if (!$fullAccess) {
            if ($isManager) {
                $this->andWhere([
                    'IN',
                    'cch_owner_user_id',
                    EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($user->getId())
                ]);
            } else {
                $this->andWhere(['cch_owner_user_id' => $user->getId()]);
            }
        }
        return $this;
    }

    public function byId(int $id): self
	{
		return $this->andWhere(['cch_id' => $id]);
	}
}
