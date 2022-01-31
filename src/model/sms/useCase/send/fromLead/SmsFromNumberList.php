<?php

namespace src\model\sms\useCase\send\fromLead;

use common\models\DepartmentPhoneProject;
use common\models\Project;
use common\models\query\DepartmentPhoneProjectQuery;
use common\models\query\UserProjectParamsQuery;
use common\models\UserProjectParams;
use src\model\phoneList\entity\PhoneList;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class SmsFromNumberList
 *
 * @property array|null $list
 * @property int $userId
 * @property int $projectId
 * @property int $departmentId
 */
class SmsFromNumberList
{
    private const SORT_GENERAL_FIRST = SORT_DESC;
    private const SORT_PERSONAL_FIRST = SORT_ASC;

    /** @var SmsFromNumber[]|null */
    private ?array $list = null;

    private int $userId;
    private int $projectId;
    private int $departmentId;

    public function __construct(int $userId, int $projectId, int $departmentId)
    {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
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
     * @return SmsFromNumber[]
     */
    public function getList(): array
    {
        if ($this->list !== null) {
            return $this->list;
        }

        $this->list = [];

        $phones = (new Query())
            ->select(['project_id', 'phone_list_id', 'phone', 'type_id', 'type', Project::tableName() . '.name as project', 'department_id'])
            ->from(self::getUserPhones($this->userId, $this->projectId)->union(self::getDepartmentPhones($this->projectId, $this->departmentId)))
            ->innerJoin(Project::tableName(), 'id = project_id')
            ->orderBy(['type_id' => self::SORT_PERSONAL_FIRST])
            ->all();

        foreach ($phones as $phone) {
            $this->list[] = SmsFromNumber::createFromRow($phone);
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
            ->select(['dpp_project_id as project_id', 'dpp_phone_list_id as phone_list_id', 'pl_phone_number as phone', 'dpp_dep_id as department_id'])
            ->addSelect(new Expression(SmsFromNumber::GENERAL_ID . ' as type_id, "' . SmsFromNumber::GENERAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = dpp_phone_list_id')
            ->andWhere(['dpp_project_id' => $projectId, 'dpp_dep_id' => $departmentId, 'dpp_default' => DepartmentPhoneProject::DPP_DEFAULT_TRUE]);
    }

    private static function getUserPhones(int $userId, int $projectId): UserProjectParamsQuery
    {
        return UserProjectParams::find()
            ->select(['upp_project_id as project_id', 'upp_phone_list_id as phone_list_id', 'pl_phone_number as phone', 'upp_dep_id as department_id'])
            ->addSelect(new Expression(SmsFromNumber::PERSONAL_ID . ' as type_id, "' . SmsFromNumber::PERSONAL . '" as type'))
            ->innerJoin(PhoneList::tableName(), 'pl_id = upp_phone_list_id')
            ->andWhere(['upp_user_id' => $userId, 'upp_project_id' => $projectId]);
    }
}
