<?php

namespace src\model\phoneList\services;

use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Project;
use common\models\query\DepartmentPhoneProjectQuery;
use common\models\query\UserProjectParamsQuery;
use common\models\UserProjectParams;
use src\model\department\department\CallDefaultPhoneType;
use src\model\phoneList\entity\PhoneList;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class AvailablePhoneNumberList
 *
 * @property array|null $list
 * @property int $userId
 * @property int $projectId
 * @property int $departmentId
 * @property CallDefaultPhoneType $callDefaultPhoneType
 */
class AvailablePhoneNumberList
{
    private const SORT_GENERAL_FIRST = SORT_DESC;
    private const SORT_PERSONAL_FIRST = SORT_ASC;

    /** @var AvailablePhoneNumber[]|null */
    private ?array $list = null;

    private int $userId;
    private int $projectId;
    private int $departmentId;
    private ?CallDefaultPhoneType $callDefaultPhoneType;

    public function __construct(int $userId, int $projectId, int $departmentId, ?CallDefaultPhoneType $callDefaultPhoneType)
    {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->callDefaultPhoneType = $callDefaultPhoneType;
    }

    public function getFormattedList(): array
    {
        $list = [];
        foreach ($this->getList() as $phone) {
            $list[$phone->phone] = $phone->format();
        }
        return $list;
    }

    /**
     * @return AvailablePhoneNumber[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        if ($this->callDefaultPhoneType && $this->callDefaultPhoneType->isGeneral()) {
            $sort = self::SORT_GENERAL_FIRST;
        } else {
            $sort = self::SORT_PERSONAL_FIRST;
        }

        $phones = (new Query())
            ->select([
                'project_id',
                'phone_list_id',
                'phone',
                'type_id',
                'type',
                'project.name as project',
                'department_id',
                'dep_name as department'
            ])
            ->from(self::getUserPhones($this->userId, $this->projectId)->union(self::getDepartmentPhones($this->projectId, $this->departmentId)))
            ->innerJoin(['project' => Project::tableName()], 'project.id = project_id')
            ->leftJoin(Department::tableName(), 'dep_id = department_id')
            ->orderBy(['type_id' => $sort])
            ->all();

        foreach ($phones as $phone) {
            $this->list[] = AvailablePhoneNumber::createFromRow($phone);
        }

        return $this->list;
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

    private static function getDepartmentPhones(int $projectId, int $departmentId): DepartmentPhoneProjectQuery
    {
        return DepartmentPhoneProject::find()
            ->select([
                'dpp_project_id as project_id',
                'dpp_phone_list_id as phone_list_id',
                'pl_phone_number as phone',
                'dpp_dep_id as department_id'
            ])
            ->addSelect(new Expression(AvailablePhoneNumber::GENERAL_ID . ' as type_id, "' . AvailablePhoneNumber::GENERAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = dpp_phone_list_id')
            ->andWhere([
                'dpp_enable' => true,
                'pl_enabled' => true,
                'dpp_project_id' => $projectId,
                'dpp_default' => DepartmentPhoneProject::DPP_DEFAULT_TRUE,
            ])
            ->andWhere([
                'OR',
                ['dpp_dep_id' => $departmentId],
                ['IS', 'dpp_dep_id', null],
            ]);
    }

    private static function getUserPhones(int $userId, int $projectId): UserProjectParamsQuery
    {
        return UserProjectParams::find()
            ->select([
                'upp_project_id as project_id',
                'upp_phone_list_id as phone_list_id',
                'pl_phone_number as phone',
                'upp_dep_id as department_id'
            ])
            ->addSelect(new Expression(AvailablePhoneNumber::PERSONAL_ID . ' as type_id, "' . AvailablePhoneNumber::PERSONAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = upp_phone_list_id')
            ->andWhere([
                'pl_enabled' => true,
                'upp_user_id' => $userId,
                'upp_project_id' => $projectId,
            ]);
    }
}
