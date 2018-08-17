<?php
namespace backend\controllers;

use common\controllers\DefaultController;
use common\models\ApiLog;
use common\models\Lead;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends DefaultController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                ],
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() : string
    {

        $days = 30;
        $dataStatsDone = Lead::find()->select("COUNT(*) AS done_count, DATE(created) AS created_date") //, SUM(tr_total_price) AS sum_price
        /*->where([
            'status' => [
                Lead::STATUS_,
            ],
        ])*/
            //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy('DATE(created)')
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


            
        $dataStatsPending = Lead::find()->select("COUNT(*) AS pending_count, DATE(created) AS created_date") //, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_PENDING,
            ],
        ])
            //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();



        $dataStatsBooked = Lead::find()->select("COUNT(*) AS book_count, DATE(created) AS created_date") //, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_BOOKED,
            ],
        ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsSold = Lead::find()->select("COUNT(*) AS sold_count, DATE(created) AS created_date") //, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_SOLD,
            ],
        ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


            //print_r($dataStatsPending);

        $dataStats = [];

        foreach ($dataStatsDone as $item) {
            $item['pending_count'] = 0;
            $item['book_count'] = 0;
            $item['sold_count'] = 0;

            $dataStats[$item['created_date']] =  $item;
        }

        foreach ($dataStatsPending as $item) {
            $item['done_count'] = 0;
            $item['book_count'] = 0;
            if(isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['pending_count'] = $item['pending_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }



        foreach ($dataStatsBooked as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['sold_count'] = 0;

            if(isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['book_count'] = $item['book_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }


        foreach ($dataStatsSold as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['book_count'] = 0;

            if(isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['sold_count'] = $item['sold_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }





        //print_r($dataStatsBooked);        exit;

        ksort($dataStats);

        // VarDumper::dump($dataStatsDone, 10, true); exit;


        //$dataStats = array_reverse($dataStats);

        //$dataSources = [];


        $dataSources = ApiLog::find()->select('COUNT(*) AS cnt, al_user_id')
            /*->where([
                'tr_status_id' => [
                    Trip::STATUS_DONE,
                    Trip::STATUS_ARCHIVE
                ],
            ])*/
            ->andWhere(['>=', 'DATE(al_request_dt)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['al_user_id'])
            ->orderBy('cnt DESC')
            ->asArray()->all();





        $dataEmployee = Lead::find()->select("COUNT(*) AS cnt, employee_id") //, SUM(tr_total_price) AS sum_price
        /*->where([
            'status' => [
                Lead::STATUS_BOOKED,
            ],
        ])*/
            //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['employee_id'])
            ->orderBy('cnt DESC')
            ->limit(30)->asArray()->all();


        $dataEmployeeSold = Lead::find()->select("COUNT(*) AS cnt, employee_id") //, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_SOLD,
            ],
        ])
        //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
        ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-".$days." days"))])
            ->groupBy(['employee_id'])
            ->orderBy('cnt DESC')
            ->limit(30)->asArray()->all();


         //print_r($dataEmployee); exit;


        /*$dataGds2 = Trip::find()->select('COUNT(*) AS cnt, tr_gds_id')
            ->where([
                'tr_status_id' => [
                    Trip::STATUS_ERROR,
                    Trip::STATUS_INIT,
                ],
            ])
            ->andWhere("tr_gds_id > 0 AND DATE(tr_created_dt) >= DATE(NOW() - interval '".$days." days')")
            ->groupBy(['tr_gds_id'])
            ->orderBy('cnt DESC')
            ->asArray()->all();*/



        return $this->render('index', ['dataStats' => $dataStats, 'dataSources' => $dataSources, 'dataEmployee' => $dataEmployee, 'dataEmployeeSold' => $dataEmployeeSold]);
    }

    public function actionLogout()
    {
        return parent::actionLogout();
    }

    public function actionLogin()
    {
        return parent::actionLogin();
    }

    public function actionProfile()
    {
        return parent::actionProfile();
    }
}
