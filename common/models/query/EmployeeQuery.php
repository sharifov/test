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
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\leadRedial\entity\CallRedialUserAccess;
use sales\model\user\entity\userStatus\UserStatus;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\db\Expression;
use yii\db\Query;

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

    /**
     * @param bool $leadIsBusiness
     * @param int $projectId
     * @param int $departmentId
     * @param int $redialUserAccessExpiredSeconds
     * @param int $limit
     * @return array []
     */
    public static function getAgentsForRedialCallByLead(
        bool $leadIsBusiness,
        int $projectId,
        int $departmentId,
        int $redialUserAccessExpiredSeconds,
        int $limit
    ): array {
        $query = Employee::find();
        $query->select([
            Employee::tableName() . '.id',
            'up.up_skill'
        ]);
        $query->groupBy([
            'id',
            'up_skill',
            'uparams.up_call_user_level'
        ]);

        $joinedField = Employee::tableName() . '.id';

        $from = date("Y-m-01");
        $to = date("Y-m-d");

        $grossProfitQuery = self::getSalesQuery($from, $to);

        $subQuery = (new Query())->select([
            'gross_profit' => 'sum(sales.gross_profit)'
        ])->from([
            'sales' => $grossProfitQuery
        ]);

        $query->addSelect([
            'gross_profit' => $subQuery
        ]);

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
        $query->join('join', [
            'd' => Department::tableName()
        ], 'ud.ud_dep_id = d.dep_id and d.dep_id = :departmentId', [
            'departmentId' => $departmentId
        ]);
        $query->leftJoin([
            'crua' => CallRedialUserAccess::tableName()
        ], $joinedField . ' = crua.crua_user_id');

        $query->andWhere('(crua.crua_created_dt is null or time_to_sec(TIMEDIFF(:now, crua.crua_created_dt)) > :redialUserAccessExpiredSeconds)', [
            'now' => (new \DateTime())->format('Y-m-d H:i:s'),
            'redialUserAccessExpiredSeconds' => $redialUserAccessExpiredSeconds
        ]);

        if ($leadIsBusiness) {
            $query->orderBy([
                'up_skill' => SORT_DESC,
            ]);
        }

        $query->addOrderBy([
            'uparams.up_call_user_level' => SORT_DESC,
            'gross_profit' => SORT_DESC
        ]);
        $query->limit($limit);
        return $query->asArray()->all();
    }

    private static function getSalesQuery(string $from, string $to): Query
    {
        $query = new Query();
        $query->select([
            '(ROUND(if(sp.`owner_share` is null, 1, sp.`owner_share`) * (final_profit - agents_processing_fee), 2)) as gross_profit',
        ]);
        $query->from(Lead::tableName());
        $query->leftJoin([
            'sp' => (new Query())->select(['id', '(100 - sum(ps_percent)) / 100 as owner_share'])
                ->from(Lead::tableName())
                ->innerJoin('profit_split', 'ps_lead_id = id')
                ->where(['status' => Lead::STATUS_SOLD])
                ->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to])
                ->andWhere('employee_id = ' . Employee::tableName() . '.id')
                ->groupBy(['id'])
        ], 'sp.id = leads.id');
        $query->where(['status' => Lead::STATUS_SOLD]);
        $query->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);
        $query->andWhere('employee_id = ' . Employee::tableName() . '.id');

        $complementaryQuery = new Query();
        $complementaryQuery->select([
            '(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2)) as gross_profit',
        ]);
        $complementaryQuery->from(Lead::tableName());
        $complementaryQuery->innerJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . Employee::tableName() . '.id');
        $complementaryQuery->where(['status' => Lead::STATUS_SOLD]);
        $complementaryQuery->andWhere(['BETWEEN', 'DATE(l_status_dt)', $from, $to]);
        $query->union($complementaryQuery, true);
        return $query;
    }

//    public function supervisorsByGroups(array $groups)
//  {
//      return $this->leftJoin('auth_assignment','auth_assignment.user_id = id')->andWhere(['auth_assignment.item_name' => Employee::SUPE])->innerJoin(UserGroup::tableName(), new Expression(''))
//  }
}
