<?php

namespace frontend\controllers;

use src\helpers\app\AppHelper;
use src\model\phoneNumberRedial\abac\PhoneNumberRedialAbacObject;
use src\model\phoneNumberRedial\PhoneNumberRedialRepository;
use src\model\phoneNumberRedial\useCase\createMultiple\CreateMultipleForm;
use Yii;
use src\model\phoneNumberRedial\entity\PhoneNumberRedial;
use src\model\phoneNumberRedial\entity\PhoneNumberRedialSearch;
use frontend\controllers\FController;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class PhoneNumberRedialCrudController
 * @package frontend\controllers
 *
 * @property-read PhoneNumberRedialRepository $repository
 */
class PhoneNumberRedialCrudController extends FController
{
    private PhoneNumberRedialRepository $repository;

    public function __construct($id, $module, PhoneNumberRedialRepository $repository, $config = [])
    {
        $this->repository = $repository;
        parent::__construct($id, $module, $config);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-selected' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new PhoneNumberRedialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($pnr_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($pnr_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneNumberRedial();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'pnr_id' => $model->pnr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreateMultiple()
    {
        $model = new CreateMultipleForm();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate()) {
                try {
                    foreach ($model->phonePattern as $value) {
                        foreach ($model->phoneNumber as $numberId) {
                            $phoneNumberRedial = PhoneNumberRedial::create(
                                $model->projectId,
                                $model->name,
                                (string)$value,
                                (int)$numberId,
                                $model->priority,
                                $model->enabled
                            );
                            $this->repository->save($phoneNumberRedial);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Multiple rows created successfully');
                    return $this->redirect(['index']);
                } catch (\RuntimeException $e) {
                    $model->addError('general', $e->getMessage());
                }
            }
        }

        return $this->render('create-multiple', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($pnr_id)
    {
        $model = $this->findModel($pnr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'pnr_id' => $model->pnr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $pnr_id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($pnr_id): Response
    {
        $this->findModel($pnr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSelectAll(): Response
    {
        if (Yii::$app->request->isAjax) {
            $result = (new PhoneNumberRedialSearch())->searchIds(Yii::$app->request->queryParams);
            return $this->asJson($result);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     */
    public function actionDeleteSelected(): Response
    {
        $abac = Yii::$app->abac;
        if (!$abac->can(null, PhoneNumberRedialAbacObject::OBJ_PHONE_NUMBER_REDIAL, PhoneNumberRedialAbacObject::ACTION_MULTIPLE_DELETE)) {
            throw new NotAcceptableHttpException('Access denied');
        }

        $items = Yii::$app->request->post('selection');

        if (Yii::$app->request->isAjax && !empty($items) && is_array($items)) {
            $result = [];
            foreach ($items as $value) {
                if ($phoneNumberRedial = $this->findModel($value)) {
                    try {
                        $phoneNumberRedial->delete();
                        $result[] = $value;
                    } catch (\RuntimeException $throwable) {
                        $messageData = AppHelper::throwableLog($throwable);
                        $messageData['items'] = $items;
                        \Yii::warning($messageData, 'PhoneNumberRedialCrudController:actionDeleteSelected:Exception');
                    } catch (Throwable $throwable) {
                        $messageData = AppHelper::throwableLog($throwable);
                        $messageData['items'] = $items;
                        \Yii::warning($messageData, 'PhoneNumberRedialCrudController::actionDeleteSelected:Throwable');
                    }
                }
            }
            return $this->asJson($result);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param int $pnr_id ID
     * @return PhoneNumberRedial
     * @throws NotFoundHttpException
     */
    protected function findModel($pnr_id): PhoneNumberRedial
    {
        if (($model = PhoneNumberRedial::findOne(['pnr_id' => $pnr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
