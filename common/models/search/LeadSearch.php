<?php

namespace common\models\search;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\UserGroupAssign;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Lead;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\VarDumper;
use common\models\Quote;
use common\models\LeadFlightSegment;
use common\models\Client;
use common\models\ProjectEmployeeAccess;
use common\models\LeadFlow;
use common\models\ProfitSplit;
use common\models\TipsSplit;

/**
 * LeadSearch represents the model behind the search form of `common\models\Lead`.
 */
class LeadSearch extends Lead
{

    public $client_name;
    public $client_email;
    public $client_phone;
    public $quote_pnr;
    public $cnt;

    public $statuses = [];
    public $created_date_from;
    public $created_date_to;
    public $depart_date_from;
    public $depart_date_to;
    public $sold_date_from;
    public $sold_date_to;
    public $processing_filter;

    public $supervision_id;

    /* processing search form */
    public $email_status;
    public $quote_status;

    public $limit;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'employee_id', 'status', 'project_id', 'adults', 'children', 'infants', 'rating', 'called_expert', 'cnt', 'l_grade', 'l_answered', 'supervision_id', 'limit'], 'integer'],
            [['email_status', 'quote_status'], 'integer'],

            [['client_name', 'client_email', 'client_phone','quote_pnr'], 'string'],

            //['created_date_from', 'default', 'value' => '2018-01-01'],
            //['created_date_to', 'default', 'value' => date('Y-m-d')],

            [['uid', 'trip_type', 'cabin', 'notes_for_experts', 'created', 'updated', 'request_ip', 'request_ip_detail', 'offset_gmt', 'snooze_for', 'discount_id', 'bo_flight_id',
                'created_date_from', 'created_date_to', 'depart_date_from', 'depart_date_to', 'source_id', 'statuses', 'sold_date_from', 'sold_date_to', 'processing_filter'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    /*public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels2 = [
            'statuses' => 'Statuses',
            'created_date_from' => 'Created date from',
            'created_date_to' => 'Created date to',
        ];

        $labels = array_merge($labels, $labels2);

        return $labels;
    }*/

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search2($params)
    {
        $query = Lead::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
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
            'id' => $this->id,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,
            'called_expert' => $this->called_expert,
            'l_grade' => $this->l_grade,
            'l_answered'    => $this->l_answered

        ]);


        if($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }


        if($this->created_date_from || $this->created_date_to) {

            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }

        } else {

            if($this->created) {
                $query->andFilterWhere(['DATE(created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->depart_date_from || $this->depart_date_to) {
            $having = [];
            if ($this->depart_date_from) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($this->depart_date_from))."'";
            }
            if ($this->depart_date_to) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($this->depart_date_to))."'";
            }

            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->sold_date_from || $this->sold_date_to) {

            /*if ($this->sold_date_from) {
             $query->andFilterWhere(['>=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_from))]);
             }
             if ($this->sold_date_to) {
             $query->andFilterWhere(['<=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_to))]);
             }*/


            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_to))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                        ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if($this->quote_pnr) {
            //$subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            //$query->andWhere(['IN', 'leads.id', $subQuery]);

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
        }



        if($this->supervision_id > 0) {

            if($this->id || $this->uid || $this->client_id || $this->client_email || $this->client_phone || $this->status == Lead::STATUS_FOLLOW_UP) {

            } else {

                if($this->statuses && in_array(Lead::STATUS_FOLLOW_UP, $this->statuses) && count($this->statuses) == 1) {

                } else {

                    $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
                    $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
                    $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
                }
            }
        }

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'trip_type', $this->trip_type])
            ->andFilterWhere(['like', 'cabin', $this->cabin])
            ->andFilterWhere(['like', 'notes_for_experts', $this->notes_for_experts])
            ->andFilterWhere(['like', 'request_ip', $this->request_ip])
            ->andFilterWhere(['like', 'request_ip_detail', $this->request_ip_detail])
            ->andFilterWhere(['like', 'offset_gmt', $this->offset_gmt])
            ->andFilterWhere(['like', 'discount_id', $this->discount_id]);

        /*  $sqlRaw = $query->createCommand()->getRawSql();

        VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    public function searchAgent($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $query
        ->andWhere(['IN', Lead::tableName() . '.project_id', $projectIds])
        ;

        /*'id' => ''
        'uid' => ''
        'client_id' => ''
        'client_name' => ''
        'client_email' => ''
        'client_phone' => ''
        'bo_flight_id' => ''
        'employee_id' => ''*/


        if($this->id || $this->uid || $this->client_id || $this->client_name || $this->client_email || $this->client_phone || $this->bo_flight_id || $this->employee_id || $this->request_ip) {

        } else {
            $this->employee_id = Yii::$app->user->id;
        }

        //VarDumper::dump($params, 10, true); exit;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'source_id' => $this->source_id,
            'adults' => $this->adults,
            'children' => $this->children,
            'infants' => $this->infants,
            //'created' => $this->created,
            //'updated' => $this->updated,
            'snooze_for' => $this->snooze_for,
            'bo_flight_id' => $this->bo_flight_id,
            'rating' => $this->rating,

            'uid' => $this->uid,
            'trip_type' => $this->trip_type,
            'cabin' => $this->cabin,
            'request_ip' => $this->request_ip,
            'discount_id' => $this->discount_id,
            'l_grade' => $this->l_grade,
            'l_answered'    => $this->l_answered
        ]);


        if($this->statuses) {
            $query->andWhere(['status' => $this->statuses]);
        }

        $query->andWhere(['<>', 'status', Lead::STATUS_PENDING]);


        if($this->created_date_from || $this->created_date_to) {

            if ($this->created_date_from) {
                $query->andFilterWhere(['>=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_from))]);
            }
            if ($this->created_date_to) {
                $query->andFilterWhere(['<=', 'DATE(leads.created)', date('Y-m-d', strtotime($this->created_date_to))]);
            }

        } else {

            if($this->created) {
                $query->andFilterWhere(['DATE(created)'=> date('Y-m-d', strtotime($this->created))]);
            }
        }

        if($this->depart_date_from || $this->depart_date_to) {
            $having = [];
            if ($this->depart_date_from) {
                $having[] = "MIN(departure) >= '".date('Y-m-d', strtotime($this->depart_date_from))."'";
            }
            if ($this->depart_date_to) {
                $having[] = "MIN(departure) <= '".date('Y-m-d', strtotime($this->depart_date_to))."'";
            }

            $subQuery = LeadFlightSegment::find()->select(['DISTINCT(lead_id)'])->groupBy('lead_id')->having(implode(" AND ", $having));

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['=', 'clients.last_name', $this->client_name])
                        ->orWhere(['=', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['=', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->quote_pnr) {
            /* $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]); */

            $query->andWhere(['LIKE','leads.additional_information', new Expression('\'%"pnr":%"'.$this->quote_pnr.'"%\'')]);
        }



       /*  $sqlRaw = $query->createCommand()->getRawSql();

        VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchSold($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100,
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
        ]);

        $query
        ->andWhere(['leads.status' => Lead::STATUS_SOLD])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;


        if(!empty($this->updated)){
            $query->andFilterWhere(['=','DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if($this->sold_date_from || $this->sold_date_to) {

            /*if ($this->sold_date_from) {
             $query->andFilterWhere(['>=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_from))]);
             }
             if ($this->sold_date_to) {
             $query->andFilterWhere(['<=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_to))]);
             }*/


            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_to))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                    ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {

            $this->client_phone = preg_replace('~[^0-9\+]~', '', $this->client_phone);
            $this->client_phone = ($this->client_phone[0] === "+" ? '+' : '') . str_replace("+", '', $this->client_phone);

            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if($this->quote_pnr) {
            $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        if($this->employee_id){
            $query
            ->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = leads.id')
            ->leftJoin(TipsSplit::tableName().' ts','ts.ts_lead_id = leads.id')
            ->andWhere($leadTable.'.employee_id = '. $this->employee_id.' OR ps.ps_user_id ='.$this->employee_id.' OR ts.ts_user_id ='.$this->employee_id)
            ->groupBy(['leads.id']);
        }

       /*  $sqlRaw = $query->createCommand()->getRawSql();
        VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchBooked($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 100,
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
        ]);

        $query
        ->andWhere(['leads.status' => Lead::STATUS_BOOKED])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;


        if(!empty($this->updated)){
            $query->andFilterWhere(['=','DATE(leads.updated)', date('Y-m-d', strtotime($this->updated))]);
        }

        if($this->sold_date_from || $this->sold_date_to) {

            /*if ($this->sold_date_from) {
             $query->andFilterWhere(['>=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_from))]);
             }
             if ($this->sold_date_to) {
             $query->andFilterWhere(['<=', 'DATE(leads.updated)', date('Y-m-d', strtotime($this->sold_date_to))]);
             }*/


            $subQuery = LeadFlow::find()->select(['DISTINCT(lead_flow.lead_id)'])->where('lead_flow.status = leads.status AND lead_flow.lead_id = leads.id');

            if ($this->sold_date_from) {
                $subQuery->andFilterWhere(['>=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_from))]);
            }
            if ($this->sold_date_to) {
                $subQuery->andFilterWhere(['<=', 'DATE(lead_flow.created)', date('Y-m-d', strtotime($this->sold_date_to))]);
            }

            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        if($this->client_name) {
            $query->joinWith(['client' => function ($q) {
                if($this->client_name) {
                    $q->where(['like', 'clients.last_name', $this->client_name])
                    ->orWhere(['like', 'clients.first_name', $this->client_name]);
                }
            }]);
        }

        if($this->client_email) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['like', 'email', $this->client_email]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        if($this->client_phone) {
            $this->client_phone = ClientPhone::clearNumber($this->client_phone);
            $subQuery = ClientPhone::find()->select(['DISTINCT(client_id)'])->where(['like', 'phone', $this->client_phone]);
            $query->andWhere(['IN', 'client_id', $subQuery]);
        }

        //echo $this->created_date_from;
        if($this->quote_pnr) {
            $subQuery = Quote::find()->select(['DISTINCT(lead_id)'])->where(['=', 'record_locator', mb_strtoupper($this->quote_pnr)]);
            $query->andWhere(['IN', 'leads.id', $subQuery]);
        }

        /*if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        if($this->employee_id){
            $query
            ->leftJoin(ProfitSplit::tableName().' ps','ps.ps_lead_id = leads.id')
            ->andWhere($leadTable.'.employee_id = '. $this->employee_id.' OR ps.ps_user_id ='.$this->employee_id)
            ->groupBy(['leads.id']);
        }*/

        /*  $sqlRaw = $query->createCommand()->getRawSql();
         VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchProcessing($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC],'attributes' => ['id','updated','created','status']],
            'pagination' => [
                'pageSize' => 30,
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.employee_id' => $this->employee_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
            $leadTable.'.status' => $this->status,
            $leadTable.'.l_grade' => $this->l_grade,
            $leadTable.'.l_answered' => $this->l_answered,
        ]);


        $query
        ->andWhere(['IN','leads.status', [self::STATUS_SNOOZE, self::STATUS_PROCESSING, self::STATUS_ON_HOLD]])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;

        if($this->email_status > 0) {
            if($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 1'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
            if($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
            } else {
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
            }
        }


        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);


        /*  $sqlRaw = $query->createCommand()->getRawSql();
         VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchFollowUp($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['updated' => SORT_DESC],'attributes' => ['id','updated','created','status']],
            'pagination' => [
                'pageSize' => 30,
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.employee_id' => $this->employee_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
            $leadTable.'.status' => $this->status,
            $leadTable.'.l_grade' => $this->l_grade,
            $leadTable.'.l_answered' => $this->l_answered,
        ]);


        $query
        ->andWhere(['IN','leads.status', [self::STATUS_FOLLOW_UP]])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;

        $showAll = Yii::$app->request->cookies->getValue(Lead::getCookiesKey(), true);
        if (!$showAll) {
            $query->andWhere([
                'NOT IN', Lead::tableName() . '.id', Lead::unprocessedByAgentInFollowUp()
            ]);
        }

        if($this->email_status > 0) {
            if($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 1'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
            if($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
            } else {
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
            }
        }


        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);


        /*  $sqlRaw = $query->createCommand()->getRawSql();
         VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchInbox($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();


        // add conditions that should always apply here
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => $this->limit > 0 ? false : ['pageSize' => 20],
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.status' => $this->status,
        ]);

        $query
        ->andWhere(['IN','leads.status', [self::STATUS_PENDING]])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;


        if($this->limit > 0) {
            $query->limit($this->limit);
            //$dataProvider->setTotalCount($this->limit);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones']);





        return $dataProvider;
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchTrash($params)
    {
        $projectIds = array_keys(ProjectEmployeeAccess::getProjectsByEmployee());
        $query = Lead::find();
        $leadTable = Lead::tableName();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
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
            $leadTable.'.id' => $this->id,
            $leadTable.'.client_id' => $this->client_id,
            $leadTable.'.employee_id' => $this->employee_id,
            $leadTable.'.project_id' => $this->project_id,
            $leadTable.'.source_id' => $this->source_id,
            $leadTable.'.bo_flight_id' => $this->bo_flight_id,
            $leadTable.'.rating' => $this->rating,
            $leadTable.'.status' => $this->status,
            $leadTable.'.l_grade' => $this->l_grade,
            $leadTable.'.l_answered' => $this->l_answered,
        ]);

        $query
        ->andWhere(['IN','leads.status', [self::STATUS_TRASH]])
        ->andWhere(['IN', $leadTable . '.project_id', $projectIds])
        ;

        if($this->email_status > 0) {
            if($this->email_status == 2) {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) > 1'));
            } else {
                $query->andWhere(new Expression('(SELECT COUNT(*) FROM client_email WHERE client_email.client_id = leads.client_id) = 0'));
            }
        }

        if($this->quote_status > 0) {
            $subQuery = Quote::find()->select(['COUNT(*)'])->where('quotes.lead_id = leads.id')->andWhere(['status' => [Quote::STATUS_APPLIED, Quote::STATUS_SEND, Quote::STATUS_OPENED] ]);
            if($this->quote_status == 2) {
                //echo $subQuery->createCommand()->getRawSql(); exit;
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') > 0'));
            } else {
                $query->andWhere(new Expression('('.$subQuery->createCommand()->getRawSql().') = 0'));
            }
        }


        if($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'leads.employee_id', $subQuery]);
        }

        $query->with(['client', 'client.clientEmails', 'client.clientPhones', 'employee']);

        return $dataProvider;
    }

    public function searchEmail($params)
    {

        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'ce.email AS client_email']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'ce.email', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if($this->client_email) {
            $query->andFilterWhere(['like', 'ce.email', $this->client_email]);
        }

        $query->leftJoin('client_email AS ce', 'ce.client_id = l.client_id');
        $query->groupBy('ce.email');
        $query->having(['>', 'cnt', 1]);
        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_email' => [
                        'asc' => ['client_email' => SORT_ASC],
                        'desc' => ['client_email' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Email',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

    public function searchPhone($params)
    {

        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'cp.phone AS client_phone']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'cp.phone', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if($this->client_phone) {
            $query->andFilterWhere(['like', 'cp.phone', $this->client_phone]);
        }

        $query->leftJoin('client_phone AS cp', 'cp.client_id = l.client_id');
        $query->groupBy('cp.phone');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;



        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            //'sort' =>false, to remove the table header sorting
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'client_phone' => [
                        'asc' => ['client_phone' => SORT_ASC],
                        'desc' => ['client_phone' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Phone',
                    ],
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }


        return $dataProvider;
    }

    public function searchIp($params)
    {


        $this->load($params);

        $query = new Query();
        $query->select(['COUNT(distinct l.id) AS cnt', 'l.request_ip']);
        $query->from('leads AS l');
        $query->where(['IS NOT', 'l.request_ip', null]);
        $query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        if($this->request_ip) {
            $query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
        }

        $query->groupBy('l.request_ip');
        $query->having(['>', 'cnt', 1]);

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['cnt' => SORT_DESC],
                'attributes' => [
                    'cnt' => [
                        'asc' => ['cnt' => SORT_ASC],
                        'desc' => ['cnt' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Leads',
                    ],
                    'request_ip' => [
                        'asc' => ['request_ip' => SORT_ASC],
                        'desc' => ['request_ip' => SORT_DESC],
                        'label' => 'IP',
                    ],

                    //'request_ip'
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }


    public function searchAgentLeads($params)
    {
        $this->load($params);

        $query = new Query();
        $query->select(['e.id', 'e.username']);

        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_SOLD.') AS st_sold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_ON_HOLD.') AS st_on_hold ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_PROCESSING.') AS st_processing ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_FOLLOW_UP.') AS st_follow_up ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_TRASH.') AS st_trash ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_REJECT.') AS st_reject ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_BOOKED.') AS st_booked ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_SNOOZE.') AS st_snooze ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id AND status='.Lead::STATUS_PENDING.') AS st_pending ']);
        $query->addSelect(['(SELECT COUNT(*) FROM leads WHERE DATE(created)=DATE(now()) AND employee_id=e.id) AS all_statuses']);


        //$query->from('leads AS l');
        $query->from('employees AS e');

        //$query->where(['IS NOT', 'l.employee_id', null]);
        //$query->where(['>', 'all_statuses', 0]);

       // $query->leftJoin(['employee as e', 'l.employee_id=e.id']);

        //$query->andFilterWhere(['l.status' => [Lead::STATUS_PROCESSING, Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP, Lead::STATUS_ON_HOLD]]);

        /*if($this->request_ip) {
            $query->andFilterWhere(['like', 'l.request_ip', $this->request_ip]);
        }*/

        //$query->groupBy(['l.status', 'l.employee_id']);
        $query->having(['>', 'all_statuses', 0]);

        $totalCount = 20;

        $command = $query->createCommand();
        $sql = $command->rawSql;

        $paramsData = [
            'sql' => $sql,
            //'params' => [':publish' => 1],
            //'totalCount' => $totalCount,
            'sort' => [
                'defaultOrder' => ['st_sold' => SORT_DESC],
                'attributes' => [
                    'st_sold' => [
                        'asc' => ['st_sold' => SORT_ASC],
                        'desc' => ['st_sold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Sold',
                    ],
                    'st_on_hold' => [
                        'asc' => ['st_sst_on_holdold' => SORT_ASC],
                        'desc' => ['st_on_hold' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Hold',
                    ],
                    'st_processing' => [
                        'asc' => ['st_processing' => SORT_ASC],
                        'desc' => ['st_processing' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Processing',
                    ],
                    'st_follow_up' => [
                        'asc' => ['st_follow_up' => SORT_ASC],
                        'desc' => ['st_follow_up' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Follow up',
                    ],
                    'st_trash' => [
                        'asc' => ['st_trash' => SORT_ASC],
                        'desc' => ['st_trash' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Trash',
                    ],
                    'st_reject' => [
                        'asc' => ['st_reject' => SORT_ASC],
                        'desc' => ['st_reject' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Reject',
                    ],
                    'st_booked' => [
                        'asc' => ['st_booked' => SORT_ASC],
                        'desc' => ['st_booked' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Booked',
                    ],
                    'st_snooze' => [
                        'asc' => ['st_snooze' => SORT_ASC],
                        'desc' => ['st_snooze' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Snooze',
                    ],
                    'st_pending' => [
                        'asc' => ['st_pending' => SORT_ASC],
                        'desc' => ['st_pending' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Pending',
                    ],

                    'id' => [
                        'asc' => ['id' => SORT_ASC],
                        'desc' => ['id' => SORT_DESC],
                        'label' => 'Agent',
                    ],
                    'username' => [
                        'asc' => ['username' => SORT_ASC],
                        'desc' => ['username' => SORT_DESC],
                        'label' => 'Agent',
                    ],

                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ];

        $dataProvider = new SqlDataProvider($paramsData);
        return $dataProvider;
    }

}
