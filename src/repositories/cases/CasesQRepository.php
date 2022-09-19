<?php

namespace src\repositories\cases;

use common\models\CaseSale;
use common\models\Employee;
use common\models\query\CaseSaleQuery;
use common\models\UserGroupAssign;
use modules\cases\src\abac\CasesAbacObject;
use modules\cases\src\abac\dto\CasesAbacDto;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeGroupAccess;
use src\access\EmployeeProjectAccess;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\entities\cases\CasesQSearch;
use src\entities\cases\CasesStatus;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use src\helpers\setting\SettingHelper;
use Yii;

class CasesQRepository
{
    private const CACHE_DURATION = 300;

    /**
     * @param Employee $user
     * @return int
     */
    public function getPendingCount(Employee $user): int
    {
        return $this->getPendingQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getPendingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_PENDING]);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'pending';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Pending Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Pending Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Pending Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Pending Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getInboxCount(Employee $user): int
    {
        return $this->getInboxQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getBaseQuery(Employee $user, array $allowedStatusList = [CasesStatus::STATUS_PENDING]): ActiveQuery
    {
        $query = CasesQSearch::find()
            ->andWhere(['IN', 'cs_status', $allowedStatusList])
            ->andWhere(['<>', 'cs_is_automate', true]);


        if ($user->isAdmin()) {
            return $query;
        }

        $query->andWhere($this->createSubQuery($user->id, []));

        return $query;
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getInboxQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()
            ->andWhere(['cs_status' => CasesStatus::STATUS_PENDING])
            ->andWhere(['<>', 'cs_is_automate', true]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'inbox';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Inbox Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Inbox Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Inbox Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Inbox Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getErrorCount(Employee $user): int
    {
        return $this->getErrorQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getErrorQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_ERROR]);

        /*if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'error';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Error Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Error Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Error Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Error Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getAwaitingCount(Employee $user): int
    {
        return $this->getAwaitingQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getAwaitingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_AWAITING]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'awaiting';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Awaiting Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Awaiting Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Awaiting Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Awaiting Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getAutoProcessingCount(Employee $user): int
    {
        return $this->getAutoProcessingQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getAutoProcessingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_AUTO_PROCESSING]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'auto-processing';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases AutoProcessing Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner AutoProcessing Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner AutoProcessing Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty AutoProcessing Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getNeedActionCount(Employee $user): int
    {
        return $this->getNeedActionQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getNeedActionQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()
            ->andWhere(['cs_need_action' => true])
            ->andWhere(['<>', 'cs_status', CasesStatus::STATUS_PENDING])
            ->andWhere(['<>', 'cs_is_automate', true]);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'need_action';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases NeedAction Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner NeedAction Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner NeedAction Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty NeedAction Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFollowUpCount(Employee $user): int
    {
        return $this->getFollowUpQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getFollowUpQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_FOLLOW_UP]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'follow-up';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases FollowUp Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner FollowUp Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner FollowUp Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty FollowUp Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getProcessingCount(Employee $user): int
    {
        return $this->getProcessingQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getProcessingQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->select('cases.*')->andWhere(['cs_status' => CasesStatus::STATUS_PROCESSING]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$all = Auth::can('cases-q/processing/list/all');
        $owner = Auth::can('cases-q/processing/list/owner');
        $group = Auth::can('cases-q/processing/list/group');
        $empty = Auth::can('cases-q/processing/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'processing';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Processing Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Processing Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Processing Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Processing Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getSolvedCount(Employee $user): int
    {
        return $this->getSolvedQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getSolvedQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->where(['cs_status' => CasesStatus::STATUS_SOLVED]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$all = Auth::can('cases-q/solved/list/all');
        $owner = Auth::can('cases-q/solved/list/owner');
        $group = Auth::can('cases-q/solved/list/group');
        $empty = Auth::can('cases-q/solved/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'solved';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Solved Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Solved Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Solved Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Solved Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getTrashCount(Employee $user): int
    {
        return $this->getTrashQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getTrashQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => CasesStatus::STATUS_TRASH]);

        if ($user->isAdmin()) {
            return $query;
        }

        /*$all = Auth::can('cases-q/trash/list/all');
        $owner = Auth::can('cases-q/trash/list/owner');
        $group = Auth::can('cases-q/trash/list/group');
        $empty = Auth::can('cases-q/trash/list/empty');

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;*/

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'trash';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Trash Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Trash Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Trash Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Trash Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getUnidentifiedCount(Employee $user): int
    {
        return $this->getUnidentifiedQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getUnidentifiedQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find()->andWhere(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]]);
        $query->joinWith(['client', 'caseSale']);
        $query->andWhere(['css_cs_id' => null]);

        $query->orderBy([new \yii\db\Expression('FIELD (cs_status, ' . CasesStatus::STATUS_PENDING . ', ' . CasesStatus::STATUS_FOLLOW_UP . ', ' . CasesStatus::STATUS_PROCESSING . ')')]);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'unidentified';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases Unidentified Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner Unidentified Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner Unidentified Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty Unidentified Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getFirstPriorityCount(Employee $user): int
    {
        return $this->getFirstPriorityQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getFirstPriorityQuery(Employee $user): Query
    {
        $query = CasesQSearch::find();
        $query->select('*')->from([
            'dd' => (new Query())->select(['cs.*', 'DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'])->from([
                'cs' => (new Query())->select('cases.*')->from('cases')
                    ->innerJoin('case_sale', 'cs_id = css_cs_id')
                    ->where(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]])
                    ->groupBy('cs_id')
            ])->leftJoin([
                'sale_out' => (new Query())->select('css_cs_id, MIN(css_out_date) AS last_out_date')
                    ->from('case_sale')
                    ->innerJoin('cases', 'case_sale.css_cs_id = cases.cs_id')
                    ->where('css_out_date >= SUBDATE(CURDATE(), ' . SettingHelper::getCasePastDepartureDate() . ')')
                    ->groupBy('css_cs_id')
            ], 'cs.cs_id = sale_out.css_cs_id')
                ->leftJoin([
                    'sale_in' => (new Query())->select('css_cs_id, MIN(css_in_date) AS last_in_date')
                        ->from('case_sale')
                        ->innerJoin('cases', 'case_sale.css_cs_id = cases.cs_id')
                        ->where('css_in_date >= SUBDATE(CURDATE(), ' . SettingHelper::getCasePastDepartureDate() . ')')
                        ->groupBy('css_cs_id')
                ], 'cs.cs_id = sale_in.css_cs_id')
        ])
        ->where(['not', ['nextFlight' => null]])
        ->andWhere('nextFlight <= ADDDATE(CURDATE(), ' . SettingHelper::getCasePriorityDays() . ')')
        ->andWhere(['<>', 'cs_is_automate', true])
        ->orderBy(['nextFlight' => SORT_ASC]);
        $query->joinWith(['client']);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'first-priority';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases FirstPriority Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner FirstPriority Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner FirstPriority Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty FirstPriority Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getSecondPriorityCount(Employee $user): int
    {
        return $this->getSecondPriorityQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getSecondPriorityQuery(Employee $user): Query
    {
        $query = CasesQSearch::find();
        $query->select('*')->from([
            'dd' => (new Query())->select(['cs.*', 'DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'])->from([
                'cs' => (new Query())->select('cases.*')->from('cases')
                    ->innerJoin('case_sale', 'cs_id = css_cs_id')
                    ->where(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]])
                    ->groupBy('cs_id')
            ])->leftJoin([
                'sale_out' => (new Query())->select('css_cs_id, MIN(css_out_date) AS last_out_date')
                    ->from('case_sale')
                    ->innerJoin('cases', 'case_sale.css_cs_id = cases.cs_id')
                    ->where('css_out_date > ADDDATE(CURDATE(), ' . SettingHelper::getCasePriorityDays() . ')')
                    ->groupBy('css_cs_id')
            ], 'cs.cs_id = sale_out.css_cs_id')
                ->leftJoin([
                    'sale_in' => (new Query())->select('css_cs_id, MIN(css_in_date) AS last_in_date')
                        ->from('case_sale')
                        ->innerJoin('cases', 'case_sale.css_cs_id = cases.cs_id')
                        ->where('css_in_date > ADDDATE(CURDATE(), ' . SettingHelper::getCasePriorityDays() . ')')
                        ->groupBy('css_cs_id')
                ], 'cs.cs_id = sale_in.css_cs_id')
        ])
            ->where(['not', ['nextFlight' => null]])
            ->andWhere('nextFlight > ADDDATE(CURDATE(), ' . SettingHelper::getCasePriorityDays() . ')')
            ->andWhere(['<>', 'cs_is_automate', true])
            ->orderBy(['cs_need_action' => SORT_DESC, 'nextFlight' => SORT_ASC]);
        $query->joinWith(['client']);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'second-priority';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases SecondPriority Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner SecondPriority Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner SecondPriority Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty SecondPriority Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return int
     */
    public function getPassDepartureCount(Employee $user): int
    {
        return $this->getPassDepartureQuery($user)->cache(self::CACHE_DURATION)->count();
    }

    public function getPassDepartureQuery(Employee $user): Query
    {
        $query = CasesQSearch::find();
        $query->select('*')->from([
            'dd' => (new Query())->select(['cs.*', 'GREATEST(last_in_date, last_out_date) AS nextFlight'])->from([
                'cs' => (new Query())->select(['cases.*', 'MAX(css_out_date) AS last_out_date', 'MAX(css_in_date) AS last_in_date'])
                    ->from('cases')
                    ->innerJoin('case_sale', 'cs_id = css_cs_id')
                    ->where(['cs_status' => [CasesStatus::STATUS_PENDING, CasesStatus::STATUS_PROCESSING, CasesStatus::STATUS_FOLLOW_UP]])
                    ->groupBy('cs_id')
            ])
        ])
            ->where('last_out_date < SUBDATE(CURDATE(), ' . SettingHelper::getCasePastDepartureDate() . ')')
            ->andWhere('last_in_date < SUBDATE(CURDATE(), ' . SettingHelper::getCasePastDepartureDate() . ')')
            ->andWhere(['<>', 'cs_is_automate', true])
            ->orderBy(['cs_need_action' => SORT_DESC, 'nextFlight' => SORT_ASC]);
        $query->joinWith(['client']);

        if ($user->isAdmin()) {
            return $query;
        }

        $caseAbacDto = new CasesAbacDto(null);
        $caseAbacDto->mainMenuCaseBadgeName = 'pass-departure';

        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS, Access to all Cases PassDeparture Queue*/
        $all = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_ALL_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS, Access to all cases where User is Owner PassDeparture Queue*/
        $owner = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_OWNER_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS, Access to all cases where User is in common group with Owner SecondPriority Queue*/
        $group = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_GROUP_ACCESS);
        /** @abac $caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS, Access to all cases where Owner is empty PassDeparture Queue*/
        $empty = Yii::$app->abac->can($caseAbacDto, CasesAbacObject::SQL_CASE_QUEUES, CasesAbacObject::ACTION_EMPTY_OWNER_ACCESS);

        if (!$all && !$owner && !$group && !$empty) {
            $query->where('0 = 1');
            return $query;
        }

        if (!$all) {
            $query->andWhere([
                'OR',
                $owner ? $this->isOwner($user->id) : [],
                $group ? ['cs_user_id' => $this->usersIdsInCommonGroups($user->id)] : [],
                $empty ? $this->freeCase() : []
            ]);
        }

        $conditions = [];

        $query->andWhere($this->createSubQuery($user->id, $conditions, $checkDepPermission = true));

        return $query;
    }

    /**
     * @param Employee $user
     * @return ActiveQuery
     */
    public function getHotQuery(Employee $user): ActiveQuery
    {
        $query = CasesQSearch::find();

        if ($user->isAdmin()) {
            return $query;
        }

        $conditions = [];

        if ($user->isSupAgent() || $user->isExAgent()) {
            $conditions = $this->freeCase();
        }

        $query->andWhere($this->createSubQuery($user->id, $conditions));

        return $query;
    }

    /**
     * @param int $joinByStatus
     * @return CaseSaleQuery
     */
    public function getNexFlightDateSubQuery(int $joinByStatus = 0): CaseSaleQuery
    {
        $query = CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_out_date) AS last_out_date'),
            ]);

        if ($joinByStatus) {
            $query->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . $joinByStatus
            );
        }

        $query->where('css_out_date >= SUBDATE(CURDATE(), 1)')
            ->orWhere('css_in_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id');

        return $query;
    }

    /**
     * @param $userId
     * @param int $cacheDuration
     * @return ActiveQuery
     */
    private function usersIdsInCommonGroups($userId, int $cacheDuration = -1): ActiveQuery
    {
        return EmployeeGroupAccess::usersIdsInCommonGroupsSubQuery($userId, $cacheDuration);
    }

    /**
     * @param $userId
     * @return array
     */
    private function isOwner($userId): array
    {
        return ['cs_user_id' => $userId];
    }

    /**
     * @return array
     */
    private function freeCase(): array
    {
        return ['cs_user_id' => null];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inProject($userId): array
    {
        return ['cs_project_id' => EmployeeProjectAccess::getProjectsSubQuery($userId)];
    }

    /**
     * @param $userId
     * @return array
     */
    private function inDepartment($userId): array
    {
        return [
            'cs_dep_id' => EmployeeDepartmentAccess::getDepartmentsSubQuery($userId)
        ];
    }

    /**
     * @param $userId
     * @param $conditions
     * @param bool $checkDepPermission
     * @return array
     */
    private function createSubQueryForNeedAction($userId, $conditions, $checkDepPermission = true): array
    {
        $depConditions = [];
        if ($checkDepPermission) {
            $depConditions = $this->inDepartment($userId);
        }

        return [
            'or',
            [
                'and',
                $this->inProject($userId),
                $depConditions,
                $conditions
            ]
        ];
    }

    private function createSubQuery($userId, $conditions, $checkDepPermission = true): array
    {
        $depConditions = [];
        if ($checkDepPermission) {
            $depConditions = $this->inDepartment($userId);
        }

        return [
            'or',
            $this->isOwner($userId),
            [
                'and',
                $this->inProject($userId),
                $depConditions,
                $conditions
            ]
        ];
    }
}
