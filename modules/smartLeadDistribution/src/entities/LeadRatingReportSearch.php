<?php

namespace modules\smartLeadDistribution\src\entities;

use common\models\Employee;
use common\models\Lead;
use common\models\UserDepartment;
use common\models\UserGroupAssign;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\repositories\lead\LeadBadgesRepository;

class LeadRatingReportSearch extends Lead
{
    public $userGroupId;
    public $userDepartmentId;
    public $ratingCategoryId;
    public $ratingPoints;
    public $createdDateRange;

    public $ratingCategoryIds;
    public $leadStatusIds;
    public $projectIds;
    public $userGroupIds;
    public $userDepartmentIds;

    private LeadBadgesRepository $leadBadgesRepository;

    public function __construct($config = [])
    {
        $this->leadBadgesRepository = new LeadBadgesRepository();

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['id', 'ratingCategoryId', 'ratingPoints', 'project_id', 'userGroupId', 'userDepartmentId'], 'integer'],
            [['ratingCategoryIds', 'leadStatusIds', 'projectIds', 'userGroupIds', 'userDepartmentIds'], 'safe'],
            ['ratingCategoryIds', 'default', 'value' => array_keys(SmartLeadDistributionService::getCategoriesAsIdName())],
            [['ratingCategoryIds', 'leadStatusIds'], 'required'],

            [['createdDateRange'], 'string'],
            [['createdDateRange'], 'required'],
            [['createdDateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'userDepartmentIds' => 'Departments',
            'userGroupIds' => 'Groups',
            'projectIds' => 'Projects',
            'leadStatusIds' => 'Statuses',
            'ratingCategoryIds' => 'Rating Categories',
        ]);
    }

    public function getDefaultDate(): string
    {
        $prevMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
        $minDate = date('Y-m-d H:i:s', $prevMonth);
        $maxDate = date('Y-m-d H:i:s', mktime(0, 0, 0, date("m") + 2, 1, date("Y")));

        return "{$minDate} - {$maxDate}";
    }

    public function search(array $params)
    {
        $leadTable = Lead::tableName();
        $query = Lead::find()
            ->select([
                'leads.status',
                'leads_amount' => 'COUNT(leads.id)',
                'lead_data.ld_field_value AS category'
            ])
            ->rightJoin(
                'lead_data',
                'leads.id = lead_data.ld_lead_id',
            )
            ->where([
                'lead_data.ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_RATING_CATEGORY
            ])
            ->groupBy([
                'leads.status',
                'lead_data.ld_field_value',
            ])
            ->asArray();

        $this->load($params);

        if (empty($this->createdDateRange)) {
            $this->createdDateRange = $this->getDefaultDate();
        }

        $createdDateParts = explode(' - ', $this->createdDateRange);

        $query->andFilterWhere(['>=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($createdDateParts[0]))])
            ->andFilterWhere(['<=', 'created', Employee::convertTimeFromUserDtToUTC(strtotime($createdDateParts[1]))]);

        if ($this->projectIds) {
            $query->andWhere([
                'IN', 'leads.project_id', $this->projectIds
            ]);
        }

        if ($this->ratingCategoryIds) {
            $query->andFilterWhere([
                'IN',
                'lead_data.ld_field_value',
                $this->ratingCategoryIds
            ]);
        }

        if ($this->userGroupIds) {
            $groupSubQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $this->userGroupIds]);
            $query->andWhere(['IN', $leadTable . '.employee_id', $groupSubQuery]);
        }

        if ($this->userDepartmentIds) {
            $userDepartmentSubQuery = UserDepartment::find()->select(['DISTINCT(ud_user_id)'])->where(['IN', 'ud_dep_id', $this->userDepartmentIds]);
            $query->andWhere(['IN', $leadTable . '.employee_id', $userDepartmentSubQuery]);
        }

        return $query->all();
    }
}
