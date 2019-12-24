<?php

namespace sales\entities\cases;

use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

/**
 * Class CasesSearch
 *
 * @property $cssSaleId
 * @property $cssBookId
 * @property $salePNR
 * @property $clientPhone
 * @property $clientEmail
 * @property $ticketNumber
 * @property $airlineConfirmationNumber
 * @property $paxFirstName
 * @property $paxLastName
 *
 * @property array $cacheSaleData
 */
class CasesSearch extends Cases
{

    public $cssSaleId;
    public $cssBookId;
    public $salePNR;
    public $clientPhone;
    public $clientEmail;
    public $ticketNumber;
    public $airlineConfirmationNumber;
    public $paxFirstName;
    public $paxLastName;

    private $cacheSaleData = [];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['cs_gid', 'string'],
            ['cs_id', 'integer'],
            ['cs_subject', 'string'],
            ['cs_category', 'string'],
            ['cs_status', 'integer'],
            ['cs_user_id', 'integer'],
            ['cs_lead_id', 'string'],
            ['cs_dep_id', 'integer'],
            ['cs_created_dt', 'string'],
            ['cs_client_id', 'integer'],
            ['cs_project_id', 'integer'],
            ['cs_last_action_dt', 'safe'],

            ['cssSaleId', 'integer'],
            ['cssBookId', 'string'],
            ['salePNR', 'string'],
            ['clientPhone', 'string'],
            ['clientEmail', 'string'],
            ['ticketNumber', 'string'],
            ['airlineConfirmationNumber', 'string'],
            ['paxFirstName', 'string'],
            ['paxLastName', 'string'],
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function search($params, $user): ActiveDataProvider
    {
        if ($user->isAdmin()) {
            return $this->searchByAdmin($params);
        }
        return $this->searchByAgent($params, $user);
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function searchByAgent($params, $user): ActiveDataProvider
    {
        $query = Cases::find()->with(['project', 'department', 'category']);

        $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments())]);
        $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cs_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        unset($dataProvider->sort->attributes['cs_lead_id']);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_category' => $this->cs_category,
            'cs_status' => $this->cs_status,
        ]);

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);

        if ($user->isExSuper() || $user->isSupSuper()) {
            if ($this->cs_user_id) {
                $query->andWhere(['cs_user_id' => Employee::find()->select(Employee::tableName() . '.id')->andWhere([Employee::tableName() . '.id' => $this->cs_user_id])]);
            }
        }

        if ($this->cssSaleId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $this->cssSaleId])]);
        }

        if ($this->ticketNumber) {
            if ($saleId = $this->getSaleIdByTicket($this->ticketNumber)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->paxFirstName) {
            if ($saleId = $this->getSaleIdByPaxFirstName($this->paxFirstName)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->paxLastName) {
            if ($saleId = $this->getSaleIdByPaxLastName($this->paxLastName)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->airlineConfirmationNumber) {
            if ($saleId = $this->getSaleIdByAcn($this->airlineConfirmationNumber)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->cssBookId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_book_id' => $this->cssBookId])]);
        }

        if ($this->salePNR) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_pnr' => $this->salePNR])]);
        }

        if ($this->clientPhone) {
            $query->andWhere(['cs_client_id' => ClientPhone::find()->select('client_id')->andWhere(['phone' => $this->clientPhone])]);
        }

        if ($this->clientEmail) {
            $query->andWhere(['cs_client_id' => ClientEmail::find()->select('client_id')->andWhere(['email' => $this->clientEmail])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }


        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    private function searchByAdmin($params): ActiveDataProvider
    {
        $query = Cases::find()->with(['project', 'department', 'category']);

        $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments())]);
        $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cs_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        unset($dataProvider->sort->attributes['cs_lead_id']);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_category' => $this->cs_category,
            'cs_status' => $this->cs_status,
            'cs_client_id' => $this->cs_client_id
        ]);

        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);

        if ($this->cs_user_id) {
            $query->andWhere(['cs_user_id' => Employee::find()->select(Employee::tableName() . '.id')->andWhere([Employee::tableName() . '.id' => $this->cs_user_id])]);
        }
        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select(Lead::tableName() . '.id')->andWhere([Lead::tableName() . '.id' => $this->cs_lead_id])]);
        }

        if ($this->cssSaleId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $this->cssSaleId])]);
        }

        if ($this->ticketNumber) {
            if ($saleId = $this->getSaleIdByTicket($this->ticketNumber)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->paxFirstName) {
            if ($saleId = $this->getSaleIdByPaxFirstName($this->paxFirstName)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->paxLastName) {
            if ($saleId = $this->getSaleIdByPaxLastName($this->paxLastName)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->airlineConfirmationNumber) {
            if ($saleId = $this->getSaleIdByAcn($this->airlineConfirmationNumber)) {
                $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $saleId])]);
            } else {
                $query->where('0=1');
            }
        }

        if ($this->cssBookId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_book_id' => $this->cssBookId])]);
        }

        if ($this->salePNR) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_pnr' => $this->salePNR])]);
        }

        if ($this->clientPhone) {
            $query->andWhere(['cs_client_id' => ClientPhone::find()->select('client_id')->andWhere(['phone' => $this->clientPhone])]);
        }

        if ($this->clientEmail) {
            $query->andWhere(['cs_client_id' => ClientEmail::find()->select('client_id')->andWhere(['email' => $this->clientEmail])]);
        }

        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }

        if ($this->cs_last_action_dt) {
            $query->andFilterWhere(['DATE(cs_last_action_dt)' => date('Y-m-d', strtotime($this->cs_last_action_dt))]);
        }

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_id' => 'ID',
            'cs_gid' => 'GID',
            'cs_project_id' => 'Project',
            'cs_subject' => 'Subject',
            'cs_category' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead ID',
            'cs_dep_id' => 'Department',
            'cs_created_dt' => 'Created',
            'cssSaleId' => 'Sale ID',
            'cssBookId' => 'Booking ID',
            'salePNR' => 'Quote PNR'
        ];
    }

    /**
     * @return array
     */
    private function getCaseSaleData(): array
    {
        if ($this->cacheSaleData) {
            return $this->cacheSaleData;
        }
        $this->cacheSaleData = CaseSale::find()->select(['css_sale_data'])->all();
        return $this->cacheSaleData;
    }

    /**
     * @param $tickerNum
     * @return int|null
     */
    private function getSaleIdByTicket($tickerNum): ?int
    {
        foreach ($this->getCaseSaleData() as $sale) {
            $decodeSale = json_decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->ticket_number, $tickerNum) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }

    /**
     * @param $firstName
     * @return int|null
     */
    private function getSaleIdByPaxFirstName($firstName): ?int
    {
        foreach ($this->getCaseSaleData() as $sale) {
            $decodeSale = json_decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->first_name, $firstName) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }

    /**
     * @param $lastName
     * @return int|null
     */
    private function getSaleIdByPaxLastName($lastName): ?int
    {
        foreach ($this->getCaseSaleData() as $sale) {
            $decodeSale = json_decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->last_name, $lastName) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }

    /**
     * @param $acn
     * @return int|null
     */
    private function getSaleIdByAcn($acn): ?int
    {
        foreach ($this->getCaseSaleData() as $sale) {
            $decodeSale = json_decode($sale['css_sale_data'], false);
            foreach ($decodeSale->itinerary as $itinerary) {
                foreach ($itinerary->segments as $segment) {
                    if (strcasecmp($segment->airlineRecordLocator, $acn) === 0) {
                        return $decodeSale->saleId;
                    }
                }
            }
        }
        return null;
    }

}
