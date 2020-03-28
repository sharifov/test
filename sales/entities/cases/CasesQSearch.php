<?php

namespace sales\entities\cases;

use common\models\CaseSale;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use sales\repositories\cases\CasesQRepository;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class CasesSearch
 *
 * @property CasesQRepository $casesQRepository
 *
 * @property string $solved_date
 * @property string $trash_date
 * @property string $last_in_date
 * @property string $last_out_date
 * @property string $saleExist
 * @property string $activeFlight
 * @property string $lastActiveDateFlight
 *
 */
class CasesQSearch extends Cases
{

    private $casesQRepository;

    public $solved_date;
    public $trash_date;

    public $last_in_date;
    public $last_out_date;
    public $saleExist;
    public $activeFlight;
    public $lastActiveDateFlight;

    /**
     * CasesSearch constructor.
     * @param array $config
     * @throws yii\base\InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->casesQRepository = Yii::createObject(CasesQRepository::class);
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
        	['cs_id', 'integer'],
            ['cs_gid', 'string'],
            ['cs_project_id', 'integer'],
            ['cs_subject', 'string'],
            ['cs_category_id', 'integer'],
            ['cs_status', 'integer'],
            ['cs_user_id', 'integer'],
            ['cs_lead_id', 'string'],
            ['cs_dep_id', 'integer'],
            ['cs_created_dt', 'string'],
            ['solved_date', 'string'],
            ['trash_date', 'string'],
            ['cs_need_action', 'boolean'],
            ['cs_order_uid', 'string'],
            [['last_in_date', 'last_out_date'], 'string'],
            [['saleExist', 'activeFlight', 'lastActiveDateFlight'], 'safe'],
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchPending($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getPendingQuery($user);

        $query->addSelect('*');
        $query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'cs_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['last_in_date'] = [
        	'asc' => ['last_in_date' => SORT_ASC],
        	'desc' => ['last_in_date' => SORT_DESC],
		];
		$dataProvider->sort->attributes['last_out_date'] = [
        	'asc' => ['last_out_date' => SORT_ASC],
        	'desc' => ['last_out_date' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			'cs_id' => $this->cs_id,
			'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
        ]);



        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchInbox($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getInboxQuery($user);
        $query->joinWith('project', true, 'INNER JOIN');

        $query->addSelect('*');
        $query->addSelect('sale.last_out_date');
        $query->addSelect('sale.last_in_date');
        $query->addSelect('NOT ISNULL(sale.css_cs_id) AS saleExist');
        $query->addSelect('sale.activeFlight');
        $query->addSelect(new Expression('
            CASE
                WHEN
                    (sale.activeFlight = 1)
                THEN
                    LEAST(sale.last_out_date, sale.last_in_date)
            END AS lastActiveDateFlight'
        ));

        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                    new Expression('
                        CASE
                            WHEN (DATE(MAX(css_out_date)) <= SUBDATE(CURDATE(), 1)) OR (DATE(MAX(css_in_date)) <= SUBDATE(CURDATE(), 1))              
                            THEN 1
                            ELSE 0
                        END AS activeFlight'),
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => [
                'saleExist' => SORT_DESC,
                'activeFlight' => SORT_DESC,
                'lastActiveDateFlight' => SORT_ASC,
                'sort_order' => SORT_DESC,
                'cs_id' => SORT_ASC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'sort_order' => [
                'asc' => ['sort_order' => SORT_ASC],
                'desc' => ['sort_order' => SORT_DESC],
            ],
            'last_in_date' =>  [
                'asc' => ['last_in_date' => SORT_ASC],
                'desc' => ['last_in_date' => SORT_DESC],
            ],
            'last_out_date' => [
                'asc' => ['last_out_date' => SORT_ASC],
                'desc' => ['last_out_date' => SORT_DESC],
            ],
            'saleExist' => [
                'asc' => ['saleExist' => SORT_ASC],
                'desc' => ['saleExist' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Sale exist',
            ],
            'activeFlight' => [
                'asc' => ['activeFlight' => SORT_ASC],
                'desc' => ['activeFlight' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Active flight',
            ],
            'lastActiveDateFlight' => [
                'asc' => ['lastActiveDateFlight' => SORT_ASC],
                'desc' => ['lastActiveDateFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Last active flight date',
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			'cs_id' => $this->cs_id,
			'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_need_action' => $this->cs_need_action,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);


        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchProcessing($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getProcessingQuery($user);

        // add conditions that should always apply here
        $query->addSelect('*');
        $query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cs_need_action' => SORT_DESC, 'cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->sort->attributes['last_in_date'] = [
            'asc' => ['last_in_date' => SORT_ASC],
            'desc' => ['last_in_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['last_out_date'] = [
            'asc' => ['last_out_date' => SORT_ASC],
            'desc' => ['last_out_date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
        	'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
			'cs_user_id' => $this->cs_user_id,
            'cs_need_action' => $this->cs_need_action,
		]);

//        if ($this->cs_user_id) {
//            $query->andWhere(['cs_user_id' => Employee::find()->select('id')->andWhere(['like', 'username', $this->cs_user_id])]);
//        }

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchFollowUp($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getFollowUpQuery($user)->addSelect(['*']);

        $query->addSelect([
            'time_left' => new Expression('if ((cs_deadline_dt IS NOT NULL), cs_deadline_dt, \'2100-01-01 00:00:00\')')
        ]);

        $query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'cs_need_action' => SORT_DESC,
                    'time_left' => SORT_ASC,
                    'cs_id' => SORT_ASC,
                ],
                'attributes' => [
                    'cs_id',
                    'cs_gid',
                    'cs_project_id',
                    'cs_subject',
                    'cs_category_id',
                    'cs_lead_id',
                    'cs_dep_id',
                    'cs_created_dt',
                    'cs_user_id',
                    'time_left',
                    'cs_need_action',
                    'cs_order_uid',
                    'last_in_date',
                    'last_out_date',
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			'cs_id' => $this->cs_id,
			'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
			'cs_user_id' => $this->cs_user_id,
			'cs_need_action' => $this->cs_need_action,
		]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchSolved($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getSolvedQuery($user);

        $query->addSelect('*');

        // add conditions that should always apply here
		$query->addSelect('b.csl_start_dt as `solved_date`');

        $query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

		$query->join('JOIN', '('.(new Query())->select(['csl_start_dt', 'csl_case_id'])
			->from(CaseStatusLog::tableName())
			->where(['csl_to_status' => CasesStatus::STATUS_SOLVED])
			->orderBy(['csl_start_dt' => 'desc'])->createCommand()->getRawSql().') as b', 'b.`csl_case_id` = `cases`.`cs_id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->sort->attributes['last_in_date'] = [
            'asc' => ['last_in_date' => SORT_ASC],
            'desc' => ['last_in_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['last_out_date'] = [
            'asc' => ['last_out_date' => SORT_ASC],
            'desc' => ['last_out_date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
        	'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
			'cs_user_id' => $this->cs_user_id,
//			'cs_updated_dt' => $this->cs_updated_dt,
        ]);

//        if ($this->cs_user_id) {
//            $query->andWhere(['cs_user_id' => Employee::find()->select('id')->andWhere(['like', 'username', $this->cs_user_id])]);
//        }

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        if ($this->solved_date) {
            $query->andFilterHaving(['DATE(solved_date)' => date('Y-m-d', strtotime($this->solved_date))]);
        }

//        if ($this->cs_updated_dt) {
//            $query->andFilterWhere(['DATE(cs_updated_dt)' => date('Y-m-d', strtotime($this->cs_updated_dt))]);
//        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        $dataProvider->sort->attributes['solved_date'] = [
        	'asc' => ['solved_date' => SORT_ASC],
        	'desc' => ['solved_date' => SORT_DESC],
		];

//        echo $query->createCommand()->getRawSql();die;

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchTrash($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getTrashQuery($user);

		$query->addSelect('*');

		// add conditions that should always apply here
		$query->addSelect('b.csl_start_dt as `trash_date`');

		$query->join('JOIN', '('.(new Query())->select(['csl_start_dt', 'csl_case_id'])
				->from(CaseStatusLog::tableName())
				->where(['csl_to_status' => CasesStatus::STATUS_TRASH])
				->orderBy(['csl_start_dt' => 'desc'])->createCommand()->getRawSql().') as b', 'b.`csl_case_id` = `cases`.`cs_id`');

		$query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cs_need_action' => SORT_DESC, 'cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $dataProvider->sort->attributes['last_in_date'] = [
            'asc' => ['last_in_date' => SORT_ASC],
            'desc' => ['last_in_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['last_out_date'] = [
            'asc' => ['last_out_date' => SORT_ASC],
            'desc' => ['last_out_date' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
			'cs_id' => $this->cs_id,
			'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
			'cs_user_id' => $this->cs_user_id,
			'cs_need_action' => $this->cs_need_action,
		]);

//        if ($this->cs_user_id) {
//            $query->andWhere(['cs_user_id' => Employee::find()->select('id')->andWhere(['like', 'username', $this->cs_user_id])]);
//        }

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

		if ($this->trash_date) {
			$query->andFilterHaving(['DATE(trash_date)' => date('Y-m-d', strtotime($this->trash_date))]);
		}

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

		$dataProvider->sort->attributes['trash_date'] = [
			'asc' => ['trash_date' => SORT_ASC],
			'desc' => ['trash_date' => SORT_DESC],
		];

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchNeedAction($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getNeedActionQuery($user)->addSelect(['*']);

        $query->addSelect([
            'time_left' => new Expression('if ((cs_deadline_dt IS NOT NULL), cs_deadline_dt, \'2100-01-01 00:00:00\')')
        ]);
        $query->addSelect('sale.last_in_date');
        $query->addSelect('sale.last_out_date');
        $query->leftJoin([
            'sale' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    'MAX(css_in_date) AS last_in_date',
                    'MAX(css_out_date) AS last_out_date',
                ])
                ->where('css_in_date IS NOT NULL')
                ->andWhere('css_out_date IS NOT NULL')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'cs_last_action_dt' => SORT_DESC,
                    'cs_id' => SORT_ASC,
                ],
                'attributes' => [
                    'cs_id',
                    'cs_gid',
                    'cs_project_id',
                    'cs_subject',
                    'cs_category_id',
                    'cs_lead_id',
                    'cs_dep_id',
                    'cs_created_dt',
                    'cs_user_id',
                    'time_left',
                    'cs_need_action',
                    'cs_status',
                    'cs_last_action_dt',
                    'cs_order_uid',
                    'last_in_date',
                    'last_out_date',
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_user_id' => $this->cs_user_id,
            'cs_need_action' => $this->cs_need_action,
            'cs_status' => $this->cs_status,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_gid' => 'GID',
            'cs_project_id' => 'Project',
            'cs_subject' => 'Subject',
            'cs_category_id' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead',
            'cs_dep_id' => 'Department',
            'cs_created_dt' => 'Created',
            'cs_updated_dt' => 'Last Action',
            'cs_deadline_dt' => 'Deadline',
            'lastSolvedDate' => 'Solved',
            'cs_need_action' => 'Need Action',
            'cs_order_uid' => 'Booking ID',
        ];
    }

}
