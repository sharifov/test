<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule\search;

use common\models\Employee;
use common\models\UserGroupAssign;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\reports\AgentShiftSummaryReport;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class AgentShiftSummaryReportSearch extends AgentShiftSummaryReport
{
    public $startDateRange;
    public $statuses = [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE];
    public $userId;
    public $userGroupId;
    public $role;

    public $uss_count;

    public function rules(): array
    {
        return [
            [['userId', 'userGroupId'], 'integer'],
            [['role'], 'string'],
            [['startDateRange'], 'string'],
            [['startDateRange'], 'required'],
            [['startDateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function getDefaultDate(): string
    {
        $prevMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
        $minDate = date('Y-m-d H:i:s', $prevMonth);
        $maxDate = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m") + 2, 1, date("Y")));

        return "{$minDate} - {$maxDate}";
    }

    /**
     * @return array{from: string, to: string}
     */
    public function getParsedStartDate(): array
    {
        $date = explode(' - ', $this->startDateRange);

        return [
            'from' => Employee::convertTimeFromUserDtToUTC(strtotime($date[0])),
            'to' => Employee::convertTimeFromUserDtToUTC(strtotime($date[1]))
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $query = self::find()->where([
            '<>',
            'employees.status',
            Employee::STATUS_DELETED
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (empty($this->startDateRange)) {
            $this->startDateRange = $this->getDefaultDate();
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->userId)) {
            $query->andWhere([
                'id' => $this->userId
            ]);
        }

        if ($this->userGroupId) {
            $query->innerJoin(
                UserGroupAssign::tableName(),
                'ugs_user_id = employees.id AND ugs_group_id = :groupId',
                ['groupId' => $this->userGroupId]
            );
        }

        if ($this->role) {
            $query->andWhere(['IN', 'employees.id', array_keys(Employee::getListByRole($this->role))]);
        }

        return $dataProvider;
    }

    public function countData(array $params = []): array
    {
        $query = UserShiftSchedule::find()
            ->select([
                'uss_sst_id',
                'uss_duration' => 'SUM(uss_duration)',
                'uss_count' => 'COUNT(*)',
            ])
            ->andWhere([
                'uss_status_id' => [UserShiftSchedule::STATUS_APPROVED, UserShiftSchedule::STATUS_DONE]
            ])
            ->groupBy(['uss_sst_id'])
            ->asArray();

        $this->load($params);

        if (empty($this->startDateRange)) {
            $this->startDateRange = $this->getDefaultDate();
        }

        if ($this->validate()) {
            $startDate = $this->getParsedStartDate();
            $query->andWhere(['between', 'DATE(uss_start_utc_dt)', $startDate['from'], $startDate['to']]);

            if (!empty($this->userId)) {
                $query->andWhere(['uss_user_id' => $this->userId]);
            }

            if (empty($this->startDateRange)) {
                $this->startDateRange = $this->getDefaultStartDateRange();
            }

            if ($this->userGroupId) {
                $query->andWhere([
                    'uss_user_id' => UserGroupAssign::find()->select('ugs_user_id')->andWhere(['ugs_group_id' => $this->userGroupId])->column()
                ]);
            }

            if ($this->role) {
                $query->andWhere(['IN', 'uss_user_id', array_keys(Employee::getListByRole($this->role))]);
            }
        }

        return ArrayHelper::index($query->all(), 'uss_sst_id');
    }
}
