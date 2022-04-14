<?php

namespace modules\cruise\controllers;

use frontend\controllers\FController;
use modules\cruise\src\useCase\updateCruiseRequest\CruiseUpdateRequestForm;
use modules\cruise\src\useCase\updateCruiseRequest\CruiseUpdateService;
use Yii;
use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruise\search\CruiseSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class CruiseController
 *
 * @property CruiseUpdateService $cruiseUpdateService
 */
class CruiseController extends FController
{
    private CruiseUpdateService $cruiseUpdateService;

    public function __construct($id, $module, CruiseUpdateService $cruiseUpdateService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->cruiseUpdateService = $cruiseUpdateService;
    }

    /**
    * @return array
    */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CruiseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Cruise();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crs_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crs_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return Cruise
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Cruise
    {
        if (($model = Cruise::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateAjax()
    {
        $id = Yii::$app->request->get('id');

        try {
            $cruise = $this->findModel($id);
        } catch (\Throwable $throwable) {
            return '<script>alert("' . $throwable->getMessage() . '")</script>';
        }

        $form = new CruiseUpdateRequestForm($cruise);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $out = '<script>$("#modal-sm").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $cruise->crs_product_id . '"});';
            try {
                $this->cruiseUpdateService->update($form);
                $out .= 'createNotifyByObject({title: "Cruise update request", type: "success", text: "Success" , hide: true});
                 setTimeout(function () {
                      $(\'.btn-add-cabin[data-product-id="' . $cruise->crs_product_id . '"]\').trigger(\'click\');
                  }, 500);';
            } catch (\DomainException $e) {
                $out .= 'createNotifyByObject({title: "Cruise update request", type: "error", text: "' . $e->getMessage() . '" , hide: true});';
            } catch (\Throwable $e) {
                $out .= 'createNotifyByObject({title: "Cruise update request", type: "error", text: "Server error" , hide: true});';
                Yii::error($e, 'CruiseController:actionUpdateAjax');
            }

            return $out . '</script>';
        }

        return $this->renderAjax('update_ajax', [
            'model' => $form,
        ]);
    }
}
