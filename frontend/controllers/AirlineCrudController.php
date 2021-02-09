<?php

namespace frontend\controllers;

use sales\helpers\app\AppHelper;
use sales\model\airline\service\AirlineService;
use Yii;
use common\models\Airline;
use sales\model\airline\entity\AirlineSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class AirlineCrudController
 */
class AirlineCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
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
    public function actionIndex(): string
    {
        $searchModel = new AirlineSearch();
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
        $model = new Airline();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
            return $this->redirect(['view', 'id' => $model->id]);
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
     * @return Airline
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Airline
    {
        if (($model = Airline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Airline not exist.');
    }

    public function actionSynchronization(): \yii\web\Response
    {
        try {
            $timeStart = microtime(true);
            set_time_limit(300);
            Yii::$app->log->targets['debug']->enabled = false;

            $data = AirlineService::synchronization();

            if ($data['error'] || count($data['errored'])) {
                $errorMessage = 'Errors: ' .  $data['error'];
                $errorMessage .= 'Errored: (' . count($data['errored']) . ') ' . implode(', ', $data['errored']) . '<br />';
                Yii::$app->getSession()->setFlash('error', $errorMessage);
            }

            $successMessage = 'Created: (' . count($data['created']) . ') ' . implode(', ', $data['created']) . '<br />';
            $successMessage .= 'Updated: (' . count($data['updated']) . ') <br />' . implode('<br />', $data['updated']);
            Yii::$app->getSession()->setFlash('success', $successMessage);

            $timeEnd = microtime(true);
            $executeTimeSeconds = number_format(round($timeEnd - $timeStart, 2), 2);
            $infoMessage = 'Execute Time: ' .  $executeTimeSeconds . ' sec <br />';
            $infoMessage .= 'Last Modified Data: (timestamp: ' .
                $data['lastModified'] . ', dateTime: ' . date('Y-m-d H:i:s', (int) $data['lastModified']) . ')';
            Yii::$app->getSession()->setFlash('info', $infoMessage);
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'AirlineCrudController:actionSynchronization'
            );
            Yii::$app->getSession()->setFlash('error', $throwable->getMessage());
        }
        return $this->redirect(['index']);
    }
}
