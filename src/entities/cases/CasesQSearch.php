<?php

namespace src\entities\cases;

use common\models\CaseSale;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use src\repositories\cases\CasesQRepository;
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
 * @property string|null $nextFlight
 * @property int|null $css_penalty_type
 * @property string|null $css_departure_dt
 * @property string $client_locale
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
    public $nextFlight;
    public $css_penalty_type;
    public $css_departure_dt;
    public $client_locale;

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
            ['cs_is_automate', 'boolean'],
            ['solved_date', 'string'],
            ['trash_date', 'string'],
            ['cs_need_action', 'boolean'],
            ['cs_order_uid', 'string'],
            ['css_penalty_type', 'integer'],
            [['last_in_date', 'last_out_date', 'css_departure_dt'], 'string'],
            [['saleExist', 'nextFlight'], 'safe'],
            [['cs_created_dt', 'css_departure_dt', 'trash_date'], 'date', 'format' => 'php:Y-m-d'],
            ['client_locale', 'string']
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
        $query->joinWith(['client']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['client_locale'] = [
            'asc' => ['cl_locale' => SORT_ASC],
            'desc' => ['cl_locale' => SORT_DESC],
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

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

        $query->joinWith(['client']);
        $query->joinWith('project', true, 'INNER JOIN');

        $query->addSelect('*');
        $query->addSelect(new Expression('
            CASE 
                WHEN (NOT ISNULL(sale_out.css_cs_id) OR NOT ISNULL(sale_in.css_cs_id))
                THEN 1
                ELSE 0
            END AS saleExist'));
        $query->addSelect(new Expression('
             DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'));

        $query->addSelect('css_penalty_type');

        $query->leftJoin([
            'penalty_departure' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_penalty_type) AS css_penalty_type'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
                )
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = penalty_departure.css_cs_id');

        $query->leftJoin([
            'sale_out' => CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_out_date) AS last_out_date'),
            ])
            ->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
            )
            ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_out.css_cs_id');

        $query->leftJoin([
            'sale_in' => CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_in_date) AS last_in_date'),
            ])
            ->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
            )
            ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_in.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'saleExist' => SORT_DESC,
                'nextFlight' => SORT_ASC,
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
            'saleExist' => [
                'asc' => ['saleExist' => SORT_ASC],
                'desc' => ['saleExist' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Sale exist',
            ],
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],
            'css_penalty_type' => [
                'asc' => ['css_penalty_type' => SORT_ASC],
                'desc' => ['css_penalty_type' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Penalty Type',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
            'css_penalty_type' => $this->css_penalty_type,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchError($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getErrorQuery($user);

        $query->joinWith(['client']);
        $query->joinWith('project', true, 'INNER JOIN');

        $query->addSelect('*');
        $query->addSelect(new Expression('
            CASE 
                WHEN (NOT ISNULL(sale_out.css_cs_id) OR NOT ISNULL(sale_in.css_cs_id))
                THEN 1
                ELSE 0
            END AS saleExist'));
        $query->addSelect(new Expression('
             DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'));

        $query->addSelect('css_penalty_type');

        $query->leftJoin([
            'penalty_departure' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_penalty_type) AS css_penalty_type'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_ERROR
                )
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = penalty_departure.css_cs_id');

        $query->leftJoin([
            'sale_out' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_out_date) AS last_out_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_ERROR
                )
                ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_out.css_cs_id');

        $query->leftJoin([
            'sale_in' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_in_date) AS last_in_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_ERROR
                )
                ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_in.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'saleExist' => SORT_DESC,
                'nextFlight' => SORT_ASC,
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
            'saleExist' => [
                'asc' => ['saleExist' => SORT_ASC],
                'desc' => ['saleExist' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Sale exist',
            ],
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],
            'css_penalty_type' => [
                'asc' => ['css_penalty_type' => SORT_ASC],
                'desc' => ['css_penalty_type' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Penalty Type',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
            'cs_is_automate' => $this->cs_is_automate,
            'css_penalty_type' => $this->css_penalty_type,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchAwaiting($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getAwaitingQuery($user);

        $query->joinWith(['client']);
        $query->joinWith('project', true, 'INNER JOIN');

        $query->addSelect('*');
        $query->addSelect(new Expression('
            CASE 
                WHEN (NOT ISNULL(sale_out.css_cs_id) OR NOT ISNULL(sale_in.css_cs_id))
                THEN 1
                ELSE 0
            END AS saleExist'));
        $query->addSelect(new Expression('
             DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'));

        $query->addSelect('css_penalty_type');

        $query->leftJoin([
            'penalty_departure' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_penalty_type) AS css_penalty_type'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AWAITING
                )
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = penalty_departure.css_cs_id');

        $query->leftJoin([
            'sale_out' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_out_date) AS last_out_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AWAITING
                )
                ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_out.css_cs_id');

        $query->leftJoin([
            'sale_in' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_in_date) AS last_in_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AWAITING
                )
                ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_in.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'saleExist' => SORT_DESC,
                'nextFlight' => SORT_ASC,
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
            'saleExist' => [
                'asc' => ['saleExist' => SORT_ASC],
                'desc' => ['saleExist' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Sale exist',
            ],
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],
            'css_penalty_type' => [
                'asc' => ['css_penalty_type' => SORT_ASC],
                'desc' => ['css_penalty_type' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Penalty Type',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
            'cs_is_automate' => $this->cs_is_automate,
            'css_penalty_type' => $this->css_penalty_type,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchAutoProcessing($params, Employee $user): ActiveDataProvider
    {
        $query = $this->casesQRepository->getAutoProcessingQuery($user);

        $query->joinWith(['client']);
        $query->joinWith('project', true, 'INNER JOIN');

        $query->addSelect('*');
        $query->addSelect(new Expression('
            CASE 
                WHEN (NOT ISNULL(sale_out.css_cs_id) OR NOT ISNULL(sale_in.css_cs_id))
                THEN 1
                ELSE 0
            END AS saleExist'));
        $query->addSelect(new Expression('
             DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'));

        $query->addSelect('css_penalty_type');

        $query->leftJoin([
            'penalty_departure' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_penalty_type) AS css_penalty_type'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AUTO_PROCESSING
                )
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = penalty_departure.css_cs_id');

        $query->leftJoin([
            'sale_out' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_out_date) AS last_out_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AUTO_PROCESSING
                )
                ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_out.css_cs_id');

        $query->leftJoin([
            'sale_in' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_in_date) AS last_in_date'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_AUTO_PROCESSING
                )
                ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_in.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'saleExist' => SORT_DESC,
                'nextFlight' => SORT_ASC,
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
            'saleExist' => [
                'asc' => ['saleExist' => SORT_ASC],
                'desc' => ['saleExist' => SORT_DESC],
                'default' => SORT_DESC,
                'label' => 'Sale exist',
            ],
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],
            'css_penalty_type' => [
                'asc' => ['css_penalty_type' => SORT_ASC],
                'desc' => ['css_penalty_type' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Penalty Type',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
            'cs_is_automate' => $this->cs_is_automate,
            'css_penalty_type' => $this->css_penalty_type,
        ]);

        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select('id')->andWhere(['uid' => $this->cs_lead_id])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

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

        $query->addSelect('css_penalty_type');
        $query->addSelect('css_departure_dt');

        $query->joinWith(['client']);
        $query->leftJoin([
            'penalty_departure' => CaseSale::find()
                ->select([
                    'css_cs_id',
                    new Expression('
                    MIN(css_penalty_type) AS css_penalty_type'),
                    new Expression('
                    MIN(css_departure_dt) AS css_departure_dt'),
                ])
                ->innerJoin(
                    Cases::tableName() . ' AS cases',
                    'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PROCESSING
                )
                ->groupBy('css_cs_id')
        ], 'cases.cs_id = penalty_departure.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cs_need_action' => SORT_DESC, 'cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'css_penalty_type' => [
                'asc' => ['css_penalty_type' => SORT_ASC],
                'desc' => ['css_penalty_type' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Penalty Type',
            ],
            'css_departure_dt' => [
                'asc' => ['css_departure_dt' => SORT_ASC],
                'desc' => ['css_departure_dt' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Departure Date Time',
            ],
            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
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
            'cs_user_id' => $this->cs_user_id,
            'cs_need_action' => $this->cs_need_action,
            'css_penalty_type' => $this->css_penalty_type,
            'date_format(css_departure_dt, "%Y-%m-%d")' => $this->css_departure_dt,
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

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
        $query->addSelect(new Expression('
            DATE(if(last_out_date IS NULL, last_in_date, LEAST(last_in_date, last_out_date))) AS nextFlight'));

        $query->joinWith(['client']);
        $query->leftJoin([
            'sale_out' => CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_out_date) AS last_out_date'),
            ])
            ->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_FOLLOW_UP
            )
            ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_out.css_cs_id');

        $query->leftJoin([
            'sale_in' => CaseSale::find()
            ->select([
                'css_cs_id',
                new Expression('
                    MIN(css_in_date) AS last_in_date'),
            ])
            ->innerJoin(
                Cases::tableName() . ' AS cases',
                'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_FOLLOW_UP
            )
            ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
            ->groupBy('css_cs_id')
        ], 'cases.cs_id = sale_in.css_cs_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
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
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],
            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

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

        $query->joinWith(['client']);
        $query->join('JOIN', '(' . (new Query())->select(['csl_start_dt', 'csl_case_id'])
            ->from(CaseStatusLog::tableName())
            ->where(['csl_to_status' => CasesStatus::STATUS_SOLVED])
            ->orderBy(['csl_start_dt' => 'desc'])->createCommand()->getRawSql() . ') as b', 'b.`csl_case_id` = `cases`.`cs_id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cs_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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
//          'cs_updated_dt' => $this->cs_updated_dt,
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        $dataProvider->sort->attributes['solved_date'] = [
            'asc' => ['solved_date' => SORT_ASC],
            'desc' => ['solved_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['client_locale'] = [
            'asc' => ['cl_locale' => SORT_ASC],
            'desc' => ['cl_locale' => SORT_DESC],
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

        $query->joinWith(['client']);
        $query->join('JOIN', '(' . (new Query())->select(['csl_start_dt', 'csl_case_id'])
                ->from(CaseStatusLog::tableName())
                ->where(['csl_to_status' => CasesStatus::STATUS_TRASH])
                ->orderBy(['csl_start_dt' => 'desc'])->createCommand()->getRawSql() . ') as b', 'b.`csl_case_id` = `cases`.`cs_id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cs_need_action' => SORT_DESC, 'cs_id' => SORT_DESC]],
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        $dataProvider->sort->attributes['trash_date'] = [
            'asc' => ['trash_date' => SORT_ASC],
            'desc' => ['trash_date' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['client_locale'] = [
            'asc' => ['cl_locale' => SORT_ASC],
            'desc' => ['cl_locale' => SORT_DESC],
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
        $query->joinWith(['client']);
        $query->addSelect([
            'time_left' => new Expression('if ((cs_deadline_dt IS NOT NULL), cs_deadline_dt, \'2100-01-01 00:00:00\')')
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
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
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['client_locale'] = [
            'asc' => ['cl_locale' => SORT_ASC],
            'desc' => ['cl_locale' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
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
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    public function searchUnidentified($params, Employee $user)
    {
        $query = $this->casesQRepository->getUnidentifiedQuery($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cs_created_dt' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['client_locale'] = [
            'asc' => ['cl_locale' => SORT_ASC],
            'desc' => ['cl_locale' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    public function searchFirstPriority($params, Employee $user)
    {
        $query = $this->casesQRepository->getFirstPriorityQuery($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                //'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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

        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    public function searchSecondPriority($params, Employee $user)
    {
        $query = $this->casesQRepository->getSecondPriorityQuery($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                //'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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

        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

        return $dataProvider;
    }

    public function searchPassDeparture($params, Employee $user)
    {
        $query = $this->casesQRepository->getPassDepartureQuery($user);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $sorting = $dataProvider->getSort();
        $sorting->attributes = array_merge($sorting->attributes, [
            'nextFlight' => [
                'asc' => ['nextFlight' => SORT_ASC],
                'desc' => ['nextFlight' => SORT_DESC],
                //'default' => SORT_ASC,
                'label' => 'Next flight date',
            ],

            'client_locale' => [
                'asc' => ['cl_locale' => SORT_ASC],
                'desc' => ['cl_locale' => SORT_DESC],
            ],
        ]);
        $dataProvider->setSort($sorting);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
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

        $query->andFilterWhere(['like', 'cl_locale', $this->client_locale]);

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
            'nextFlight' => 'Next Flight Date',
            'css_penalty_type' => 'Penalty Type',
            'css_departure_dt' => 'Departure DT',
            'cs_is_automate' => 'Auto'
        ];
    }
}
