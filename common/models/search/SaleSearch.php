<?php

namespace common\models\search;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\BackOffice;
use common\models\CaseSale;
use common\models\Lead;
use src\entities\cases\Cases;
use src\repositories\lead\LeadBadgesRepository;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * SaleSearch represents the model behind the search form API BackOffice.
 *
 * @param int $sale_id
 * @param string $first_name
 * @param string $last_name
 * @param string $phone
 * @param string $email
 * @param string $card
 * @param string $acn
 * @param string $pnr
 * @param string $ticket_number
 * @param string $booking_id
 *
 * @param LeadBadgesRepository $leadBadgesRepository
 */
class SaleSearch extends Model
{
    public $sale_id;
    public $acn;
    public $pnr;
    public $ticket_number;
    public $booking_id;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $card;
    public $project_key;


//    public $datetime_start;
//    public $datetime_end;
//    public $date_range;

    /*private $leadBadgesRepository;

    public function __construct($config = [])
    {
        $this->leadBadgesRepository = new LeadBadgesRepository();
        parent::__construct($config);
    }*/

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['datetime_start', 'datetime_end'], 'safe'],
            //[['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],

            [['sale_id', 'first_name', 'last_name', 'phone', 'email', 'card', 'acn', 'pnr', 'booking_id', 'ticket_number', 'project_key'], 'trim'],

            [['sale_id'], 'integer'],
            [['phone', 'card', 'ticket_number', 'booking_id'], 'string', 'min' => 6, 'max' => 20],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 50],
            [['acn', 'pnr'], 'string', 'min' => 6, 'max' => 6],
            [['email'], 'email'],
            [['phone'], PhoneInputValidator::class],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'sale_id' => 'Sale Id',
            'acn' => 'Airline Confirmation Number',
            'pnr' => 'Quote PNR',
            'booking_id' => 'Confirmation Number (Booking ID)',
            'ticket_number' => 'Ticket Number',

            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'card' => 'Credit Card',
        ];
        return $labels;
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * @param $params
     * @return array|ArrayDataProvider
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $searchData = [];

        $this->load($params);

        if (!$this->validate()) {
            $searchData = [];
            return new ArrayDataProvider([
                'allModels' => $searchData,
                'pagination' => false,
            ]);
        }

        $totalCount = 0;
        $pageSize = 20;
        $currentPage = Yii::$app->request->get('page', 1);

        if (isset($params['SaleSearch']) && $params['SaleSearch']) {
            //VarDumper::dump($params['SaleSearch']); exit;
            $data = [];

            if ($this->sale_id) {
                $data['sale_id'] = $this->sale_id;
            }

            if ($this->pnr) {
                $data['pnr'] = $this->pnr;
            }

            if ($this->email) {
                $data['email'] = $this->email;
            }

            if ($this->phone) {
                $data['phone'] = $this->phone;
            }

            if ($this->first_name) {
                $data['pax_first_name'] = $this->first_name;
            }

            if ($this->last_name) {
                $data['pax_last_name'] = $this->last_name;
            }

            if ($this->ticket_number) {
                $data['ticket_number'] = $this->ticket_number;
            }

            if ($this->booking_id) {
                $data['confirmation_number'] = $this->booking_id;
            }

            if ($this->acn) {
                $data['airline_confirmation_number'] = $this->acn;
            }

            if ($this->card) {
                $data['card'] = $this->card;
            }

            if ($currentPage) {
                $data['page'] = $currentPage;
            }

            if ($pageSize) {
                $data['limit'] = $pageSize;
            }

            if ($this->project_key) {
                $data['project_key'] = $this->project_key;
            }



            $response = BackOffice::sendRequest2('cs/search', $data);

            //VarDumper::dump($response); exit;

            if ($response->isOk) {
                $result = $response->data;
                if (isset($result['items']) && is_array($result['items'])) {
                    foreach ($result['items'] as $key => $item) {
                        $caseQuery = Cases::find()->select(['cs_id', 'cs_gid'])->where(['cs_order_uid' => $item['confirmationNumber']]);
                        $caseSaleQuery = CaseSale::find()->select(['cs_id', 'cs_gid'])->innerJoin(Cases::tableName(), 'cs_id = css_cs_id')->where(['css_sale_book_id' => $item['confirmationNumber']]);
                        $result['items'][$key]['relatedLeads'] = Lead::find()->select(['id', 'gid'])->where(['bo_flight_id' => $item['saleId']])->asArray()->all();
                        $result['items'][$key]['relatedCases'] = $caseQuery->union($caseSaleQuery)->asArray()->all();
                    }

                    $searchData = $result['items'];
                    $totalCount = $result['totalItems'];
                }
                //VarDumper::dump($result, 10, true);
            } else {
                //Yii::error(print_r($response->content, true), 'SaleSearch:search:BackOffice:sendRequest2');
                throw new Exception('BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
            }
        }


        if ($searchData) {
            $arr = array_fill(0, ($currentPage - 1) * $pageSize, ['items' => ['saleId' => 0]]);
            $searchData = array_merge($arr, $searchData);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchData,
            'totalCount' => $totalCount,
            //'sort'=> ['defaultOrder' => ['sale_id' => SORT_DESC]],
            /*'sort' => [
                'defaultOrder' => ['sale_id' => SORT_DESC],
                'attributes' => ['sale_id', 'pnr', 'email'],
            ],*/
            //'pagination' => false,
            'pagination' => [
                'pageSize' => $pageSize,
                //'pageSizeLimit' => 0
            ],
        ]);

        /*$dataProvider->sort->attributes['sale_id'] = [
            'asc' => ['saleId' => SORT_DESC],
            'desc' => ['saleId' => SORT_ASC],
        ];*/



        return $dataProvider;
    }
}
