<?php

namespace common\models\query;

use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\UserDepartment;
use common\models\UserOnline;
use common\models\UserParams;
use common\models\UserProfile;
use src\access\EmployeeGroupAccess;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\leadRedial\assign\SortUsers;
use src\model\leadRedial\entity\CallRedialUserAccess;
use src\model\user\entity\userStatus\UserStatus;
use src\model\userClientChatData\entity\UserClientChatData;
use src\model\userData\entity\UserData;
use src\model\userData\entity\UserDataKey;
use src\model\userStatDay\entity\UserStatDayQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class EmployeeQuery
 */
class EmployeeQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['status' => Employee::STATUS_ACTIVE]);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function byEmail(string $email): self
    {
        return $this->andWhere(['email' => $email])->limit(1);
    }

    public function joinChatUserChannel(int $channelId): EmployeeQuery
    {
        return $this->innerJoin(
            ClientChatUserChannel::tableName(),
            'ccuc_channel_id = :channelId and ccuc_user_id = id',
            ['channelId' => $channelId]
        );
    }

    public function online(string $joinedField): EmployeeQuery
    {
        return $this->innerJoin(UserOnline::tableName(), $joinedField . ' = uo_user_id');
    }

    public function exceptUser(int $userId): EmployeeQuery
    {
        return $this->andWhere(['<>', 'id', $userId]);
    }

    public function registeredInRc(): EmployeeQuery
    {
        return $this->innerJoin(
            UserClientChatData::tableName(),
            "uccd_employee_id = id AND uccd_rc_user_id IS NOT NULL AND uccd_rc_user_id <> '' and uccd_chat_status_id = :chatStatusId",
            ['chatStatusId' => UserClientChatData::CHAT_STATUS_READY]
        );
    }

    public function hasPermission(string $permission): self
    {
        return $this->innerJoin('auth_assignment', 'auth_assignment.user_id = id')
            ->innerJoin(
                'auth_item_child',
                'auth_item_child.child = :permission and auth_item_child.parent = auth_assignment.item_name',
                ['permission' => $permission]
            );
    }

    public function enabledAutoRedial(string $joinedField): self
    {
        return $this->join('join', UserProfile::tableName() . ' up', 'up.up_user_id = ' . $joinedField . ' and up.up_auto_redial = 1');
    }

    public function activePhoneStatusNotOnCall(string $joinedField): self
    {
        return $this->join('join', UserStatus::tableName() . ' us', $joinedField . ' = us.us_user_id and us_call_phone_status = 1 and us_is_on_call = 0');
    }

    public function joinUserProfile(): self
    {
        return $this->join('join', UserProfile::tableName(), 'up_user_id = employees.id');
    }

    public function addOrderByUserProfileSkillLevel(int $order): self
    {
        return $this->addOrderBy(['up_skill' => $order]);
    }

    /**
     * @param bool $leadIsBusiness
     * @param int $projectId
     * @param int|null $departmentId
     * @param int $redialUserAccessExpiredSeconds
     * @param int $limit
     * @return array []
     * @throws \Exception
     */
    public static function getAgentsForRedialCallByLead(
        bool $leadIsBusiness,
        int $projectId,
        ?int $departmentId,
        int $redialUserAccessExpiredSeconds,
        int $limit
    ): array {
        $query = Employee::find();
        $query->select([
            Employee::tableName() . '.id',
            'up.up_skill',
            'gross_profit' => 'ud_value',
//            'us_phone_ready_time',
//            'up_call_user_level',
        ]);
        $query->groupBy([
            'id',
            'up_skill',
            'uparams.up_call_user_level',
            'gross_profit'
        ]);

        $joinedField = Employee::tableName() . '.id';

        $query->leftJoin(
            UserData::tableName(),
            'ud_user_id = ' . Employee::tableName() . '.id' . ' and ud_key = :key',
            [':key' => UserDataKey::GROSS_PROFIT]
        );

        $query->enabledAutoRedial($joinedField);
        $query->online($joinedField);
        $query->activePhoneStatusNotOnCall($joinedField);
        $query->join('join', [
            'uparams' => UserParams::tableName()
        ], $joinedField . ' = uparams.up_user_id');
        $query->join('join', [
            'pea' => ProjectEmployeeAccess::tableName()
        ], $joinedField . ' = pea.employee_id');
        $query->join('join', [
            'p' => Project::tableName()
        ], 'pea.project_id = p.id and p.closed = 0 and p.id = :projectId', [
            'projectId' => $projectId
        ]);
        $query->join('join', [
            'ud' => UserDepartment::tableName()
        ], $joinedField . ' = ud.ud_user_id');
        if ($departmentId !== null) {
            $query->join('join', [
                'd' => Department::tableName()
            ], 'ud.ud_dep_id = d.dep_id and d.dep_id = :departmentId', [
                'departmentId' => $departmentId
            ]);
        }

        $query->leftJoin([
            'crua' => CallRedialUserAccess::tableName()
        ], $joinedField . ' = crua.crua_user_id');
        $query->andWhere(['IS', 'crua.crua_created_dt', null]);
//        $query->andWhere('(crua.crua_created_dt is null or time_to_sec(TIMEDIFF(:now, crua.crua_created_dt)) > :redialUserAccessExpiredSeconds)', [
//            'now' => (new \DateTime())->format('Y-m-d H:i:s'),
//            'redialUserAccessExpiredSeconds' => $redialUserAccessExpiredSeconds
//        ]);

        if ($leadIsBusiness) {
            $query->orderBy([
                'up_skill' => SORT_DESC,
            ]);
        }

        $sort = (new SortUsers())->getValue();
        if ($sort) {
            $query->addOrderBy($sort);
        }

        $query->limit($limit);
//        \Yii::info($query->createCommand()->rawSql, 'info\getAgentsForRedialCallByLead');
        return $query->asArray()->all();
    }

    public static function getSalesQuery(string $from, string $to): Query
    {
        $query = new Query();
        $query->select([
            '(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2)) as gross_profit',
            'employee_id' => 'ps_user_id'
        ]);
        $query->from(Lead::tableName());
        $query->innerJoin('profit_split', 'ps_lead_id = id');
        $query->where(['status' => Lead::STATUS_SOLD]);
        $query->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);

        return $query;
    }

    public static function getList(?int $userId = null): array
    {
        $query = Employee::find()->select(['id', 'CONCAT(username, " (", id, ")") AS username']);
        if ($userId !== null) {
            $query->andWhere(['id' => EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId)]);
        }
        $query->orderBy(['username' => SORT_ASC])->asArray()->indexBy('id');

        return ArrayHelper::map(
            $query->all(),
            'id',
            'username'
        );
    }

    public static function findByEmail(string $email): ?Employee
    {
        return Employee::findOne(['email' => $email]);
    }
}
