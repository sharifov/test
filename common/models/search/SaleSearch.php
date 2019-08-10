<?php

namespace common\models\search;

use sales\repositories\lead\LeadBadgesRepository;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;


/**
 * LeadSearch represents the model behind the search form of `common\models\Lead`.
 * @param LeadBadgesRepository $leadBadgesRepository
 */
class SaleSearch extends Model
{
    public $sale_id;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $card;
    public $acn;
    public $pnr;

    public $datetime_start;
    public $datetime_end;
    public $date_range;

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
            [['datetime_start', 'datetime_end'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['sale_id', 'first_name', 'last_name', 'phone', 'acn', 'card', 'email', 'pnr'], 'string'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {


        $labels = [
            'sale_id' => 'Sale',
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ArrayDataProvider
     */

    public function search($params)
    {

        $searchData = [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchData,
            //'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'sort' => [
                'attributes' => ['sale_id', 'pnr', 'email'],
            ],
            'pagination' => false,
            /*'pagination' => [
                'pageSize' => 10,
            ],*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $searchData = [];
            return $dataProvider;
        }



        // get the posts in the current page
        //$posts = $provider->getModels();


       /*  $sqlRaw = $query->createCommand()->getRawSql();

        VarDumper::dump($sqlRaw, 10, true); exit; */

        return $dataProvider;
    }


}
