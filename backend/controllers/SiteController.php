<?php
namespace backend\controllers;

use common\controllers\DefaultController;
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



        //$query = new Books::find()->where('author=2');
        //echo $dataStatsDone->createCommand()->getRawSql();

            //echo $dataStatsDone->r

            //->asArray()->all();


            //exit;


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
            ->limit(30)
            ->asArray()->all();





        $dataStats = [];

        foreach ($dataStatsDone as $item) {
            $item['error_count'] = 0;
            $dataStats[$item['created_date']] =  $item;
        }

        foreach ($dataStatsPending as $item) {
            $item['pending_count'] = 0;
            if(isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['pending_count'] = $item['pending_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }

        ksort($dataStats);

        // VarDumper::dump($dataStatsDone, 10, true); exit;


        //$dataStats = array_reverse($dataStats);

        $dataSources = [];


        /*$dataGds = Trip::find()->select('COUNT(*) AS cnt, tr_gds_id')
            ->where([
                'tr_status_id' => [
                    Trip::STATUS_DONE,
                    Trip::STATUS_ARCHIVE
                ],
            ])
            ->andWhere("DATE(tr_created_dt) >= DATE(NOW() - interval '".$days." days')")
            ->groupBy(['tr_gds_id'])
            ->orderBy('cnt DESC')
            ->asArray()->all();*/


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



        return $this->render('index', ['dataStats' => $dataStats, 'dataSources' => $dataSources]);
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
