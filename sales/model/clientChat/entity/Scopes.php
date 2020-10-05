<?php

namespace sales\model\clientChat\entity;

use common\models\Employee;
use sales\access\EmployeeAccessHelper;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeGroupAccess;
use sales\access\EmployeeProjectAccess;
use sales\helpers\user\UserFinder;

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
        Employee::ROLE_SUPERVISION,
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

	public function byOwner(?int $userId): self
	{
		if ($userId) {
			return $this->andWhere(['cch_owner_user_id' => $userId]);
		}
		return $this->andWhere(['cch_owner_user_id' => null]);
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

	public function expectOwner(int $id): self
	{
		return $this->andWhere(['<>', 'cch_owner_user_id', $id]);
	}

	public function byDepartment(int $dep): self
	{
		return $this->andWhere(['cch_dep_id' => $dep]);
	}

	public function withOwner(): self
	{
		return $this->andWhere(['not', ['cch_owner_user_id' => null]]);
	}

	public function byProject(int $id): self
	{
		return $this->andWhere(['cch_project_id' => $id]);
	}

	public function byUserGroupsRestriction(?Employee $user = null): self
    {
        $user = UserFinder::getOrFind($user);
        $fullAccess = EmployeeAccessHelper::entryInRoles($user, self::ROLES_FULL_ACCESS);
        $isManager = EmployeeAccessHelper::entryInRoles($user, self::ROLES_MANAGERS);

        if (!$fullAccess) {
            if ($isManager) {
                $this->andWhere([
                    'IN',
                    'cch_owner_user_id',
                    EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($user->getId(), 5 * 60)
                ]);
            } else {
                $this->andWhere(['cch_owner_user_id' => $user->getId()]);
            }
        }
        return $this;
    }

    public function byProjectRestriction(?Employee $user = null): self
    {
        $user = UserFinder::getOrFind($user);
        $fullAccess = EmployeeAccessHelper::entryInRoles($user, self::ROLES_FULL_ACCESS);
        if (!$fullAccess) {
            $this->andWhere([
                'IN',
                'cch_project_id',
                array_keys(EmployeeProjectAccess::getProjects())
            ]);
        }
        return $this;
    }

    public function byDepartmentRestriction(?Employee $user = null): self
    {
        $user = UserFinder::getOrFind($user);
        $fullAccess = EmployeeAccessHelper::entryInRoles($user, self::ROLES_FULL_ACCESS);
        if (!$fullAccess) {
            $this->andWhere([
                'IN',
                'cch_dep_id',
                array_keys(EmployeeDepartmentAccess::getDepartments())
            ]);
        }
        return $this;
    }

    public function byId(int $id): self
	{
		return $this->andWhere(['cch_id' => $id]);
	}

	public function withoutOwnerOrInTransfer(): Scopes
	{
		return $this->byOwner(null)->orWhere(['cch_status_id' => ClientChat::STATUS_TRANSFER]);
	}

    public function withUnreadMessage(bool $edgerLoading = false): self
    {
        return $this->innerJoinWith('unreadMessage', $edgerLoading);
	}

}
