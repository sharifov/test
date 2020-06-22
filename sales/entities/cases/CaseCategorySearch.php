<?php

namespace sales\entities\cases;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * Class CaseCategorySearch
 */
class CaseCategorySearch extends CaseCategory
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public const DEFAULT_RANGE = '-29 days';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['cc_id', 'cc_key', 'cc_name'], 'string'],
            [['cc_dep_id', 'cc_system'], 'integer'],
            ['cc_enabled', 'boolean'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createTimeStart', 'createTimeEnd'], 'safe'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->createTimeRange = date('Y-m-d 00:00:00', strtotime(self::DEFAULT_RANGE)) . ' - ' . date('Y-m-d 23:59:59');
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = CaseCategory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cc_id' => $this->cc_id,
            'cc_dep_id' => $this->cc_dep_id,
            'cc_system' => $this->cc_system,
            'cc_enabled' => $this->cc_enabled,
        ]);

        $query
            ->andFilterWhere(['like', 'cc_key', $this->cc_key])
            ->andFilterWhere(['like', 'cc_name', $this->cc_name]);

        return $dataProvider;
    }

    public function prepareReportData($params):SqlDataProvider
    {
        $this->load($params);
        $query = CaseCategory::find()->joinWith(['dep', 'cases']);
        $query->select([
            'cc_id', 'cc_dep_id', 'dep_name', 'cc_name',
            'SUM(IF(cs_status = '. CasesStatus::STATUS_PENDING .', 1, 0)) AS pending',
            'SUM(IF(cs_status = '. CasesStatus::STATUS_PROCESSING .', 1, 0)) AS processing',
            'SUM(IF(cs_status = '. CasesStatus::STATUS_FOLLOW_UP .', 1, 0)) AS followup',
            'SUM(IF(cs_status = '. CasesStatus::STATUS_SOLVED .', 1, 0)) AS solved',
            'SUM(IF(cs_status = '. CasesStatus::STATUS_TRASH .', 1, 0)) AS trash'
        ]);
        $query->groupBy(['cc_id']);

        if ($this->createTimeRange){
            $dateTimeStart = Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart));
            $dateTimeEnd = Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd));
            $query->andWhere(['between', 'cases.cs_created_dt', $dateTimeStart, $dateTimeEnd]);
        }

        $query->andFilterWhere([
            'cc_id' => $this->cc_id,
            'cc_dep_id' => $this->cc_dep_id,
        ]);
        $query->andFilterWhere(['like', 'cc_name', $this->cc_name]);

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            'sort' => [
                'defaultOrder' => ['cc_id' => SORT_DESC],
                'attributes' => [
                    'cc_id',
                    'cc_dep_id',
                    'cc_name',
                    'pending',
                    'processing',
                    'followup',
                    'solved',
                    'trash',
                ],
            ],
            'pagination' => [
                'pageSize' => 25,
            ],
        ];

        return new SqlDataProvider($paramsData);
    }
}
