<?php

namespace sales\model\phone;

use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Project;
use common\models\query\DepartmentPhoneProjectQuery;
use common\models\query\UserProjectParamsQuery;
use common\models\UserProjectParams;
use sales\model\department\department\DefaultPhoneType;
use sales\model\phoneList\entity\PhoneList;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class AvailablePhoneList
 *
 * @property array $list
 */
class AvailablePhoneList
{
    public const PERSONAL = 'Personal';
    public const PERSONAL_ID = 0;

    public const GENERAL = 'General';
    public const GENERAL_ID = 1;

    /** @var AvailablePhone[] */
    private array $list;

    public function __construct(int $userId, int $projectId, int $departmentId, DefaultPhoneType $defaultPhoneType)
    {
        if ($defaultPhoneType->isOnlyPersonal()) {
            $phones = $this->getUserPhones($userId, $projectId)
                ->addSelect([Project::tableName() . '.name as project'])
                ->innerJoin(Project::tableName(), 'id = upp_project_id')
                ->asArray()->all();
            foreach ($phones as $phone) {
                $this->list[] = AvailablePhone::createFromRow($phone);
            }
            return;
        }

        if ($defaultPhoneType->isOnlyGeneral()) {
            $phones = $this->getDepartmentPhones($projectId, $departmentId)
                ->addSelect([Project::tableName() . '.name as project'])
                ->innerJoin(Project::tableName(), 'id = dpp_project_id')
                ->asArray()->all();
            foreach ($phones as $phone) {
                $this->list[] = AvailablePhone::createFromRow($phone);
            }
            return;
        }

        $phones = (new Query())
            ->select(['project_id', 'phone_list_id', 'phone', 'type_id', 'type', Project::tableName() . '.name as project', 'department_id'])
            ->from($this->getUserPhones($userId, $projectId)->union($this->getDepartmentPhones($projectId, $departmentId)))
            ->innerJoin(Project::tableName(), 'id = project_id')
            ->orderBy(['type_id' => $defaultPhoneType->isGeneralFirst() ? SORT_DESC : SORT_ASC])
            ->all();
        foreach ($phones as $phone) {
            $this->list[] = AvailablePhone::createFromRow($phone);
        }
    }

    public function getFormattedList(): array
    {
        $list = [];
        foreach ($this->getList() as $phone) {
            $list[$phone->phone] = $phone->project . ' ' . ($phone->isGeneralType() ? Department::DEPARTMENT_LIST[$phone->departmentId] : self::PERSONAL)
                . ' (' . $phone->phone . ')';
        }
        return $list;
    }

    /**
     * @return AvailablePhone[]
     */
    public function getList(): array
    {
        return $this->list;
    }

    public function getFirst(): ?AvailablePhone
    {
        return $this->list[0] ?? null;
    }

    public function isExist(string $number): bool
    {
        foreach ($this->getList() as $phone) {
            if ($phone->isEqual($number)) {
                return true;
            }
        }
        return false;
    }

    private function getDepartmentPhones(int $projectId, int $departmentId): DepartmentPhoneProjectQuery
    {
        return DepartmentPhoneProject::find()
            ->select(['dpp_project_id as project_id', 'dpp_phone_list_id as phone_list_id', 'pl_phone_number as phone', 'dpp_dep_id as department_id'])
            ->addSelect(new Expression(self::GENERAL_ID . ' as type_id, "' . self::GENERAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = dpp_phone_list_id')
            ->andWhere(['dpp_project_id' => $projectId, 'dpp_dep_id' => $departmentId, 'dpp_default' => DepartmentPhoneProject::DPP_DEFAULT_TRUE]);
    }

    private function getUserPhones(int $userId, int $projectId): UserProjectParamsQuery
    {
        return UserProjectParams::find()
            ->select(['upp_project_id as project_id', 'upp_phone_list_id as phone_list_id', 'pl_phone_number as phone', 'upp_dep_id as department_id'])
            ->addSelect(new Expression(self::PERSONAL_ID . ' as type_id, "' . self::PERSONAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = upp_phone_list_id')
            ->andWhere(['upp_user_id' => $userId, 'upp_project_id' => $projectId]);
    }
}
