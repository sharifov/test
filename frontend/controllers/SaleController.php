<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\models\CaseSale;
use common\models\search\SaleSearch;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * LeadsController implements the CRUD actions for Lead model.
 */
class SaleController extends FController
{

    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }


    /**
     * @return string
     */
    public function actionSearch()
    {
        $searchModel = new SaleSearch();
        $params = Yii::$app->request->queryParams;

        try {
            $dataProvider = $searchModel->search($params);
        } catch (\Exception $exception) {
            $dataProvider = new ArrayDataProvider();
            Yii::error(VarDumper::dumpAsString([$exception->getFile(), $exception->getCode(), $exception->getMessage()]), 'SaleController:actionSearch');
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }

        //VarDumper::dump($dataProvider->allModels); exit;

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionView()
    {

        $hash = Yii::$app->request->get('h');

        $arr = explode('|', base64_decode($hash));
        $id = (int) ($arr[1] ?? 0);

        $model = $this->findSale($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['data' => $model]);
        }

        return $this->render('view', ['data' => $model]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function actionDeleteAjax(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['error' => '', 'status' => 0];

        if (Yii::$app->request->isAjax) {
            $saleId = Yii::$app->request->post('sale_id');
            $csId = Yii::$app->request->post('case_id');

            if ($sale = CaseSale::findOne(['css_cs_id' => $csId, 'css_sale_id' => $saleId])) {
                try {
                    $sale->delete();
                    $result['status'] = 1;
                } catch (\Throwable $throwable) {
                    $result['error'] = $throwable->getMessage();
                }
            }
        }
        return $result;
    }


    /**
     * @param int $id
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    protected function findSale(int $id)
    {

        try {
            $data['sale_id'] = $id;
            $response = BackOffice::sendRequest2('cs/detail', $data, 'POST', 90);

            if ($response->isOk) {
                $result = $response->data;
                //VarDumper::dump($result); exit;

                if ($result && is_array($result)) {
                    return $result;
                }
            } else {
                throw new Exception('BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
            }

        } catch (\Throwable $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        throw new NotFoundHttpException('The requested Sale does not exist.');
    }

}
