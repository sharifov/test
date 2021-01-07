<?php

namespace frontend\controllers;

use sales\model\airportLang\service\AirportLangService;
use Yii;
use sales\model\airportLang\entity\AirportLang;
use sales\model\airportLang\entity\AirportLangSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

/**
 * Class AirportLangController
 */
class AirportLangCrudController extends FController
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
                    'delete' => ['POST']
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
        $searchModel = new AirportLangSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $ail_iata
     * @param string $ail_lang
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ail_iata, $ail_lang): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ail_iata, $ail_lang),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new AirportLang();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ail_iata' => $model->ail_iata, 'ail_lang' => $model->ail_lang]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $ail_iata
     * @param string $ail_lang
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ail_iata, $ail_lang)
    {
        $model = $this->findModel($ail_iata, $ail_lang);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ail_iata' => $model->ail_iata, 'ail_lang' => $model->ail_lang]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $ail_iata
     * @param string $ail_lang
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ail_iata, $ail_lang): Response
    {
        $this->findModel($ail_iata, $ail_lang)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $ail_iata
     * @param string $ail_lang
     * @return AirportLang
     * @throws NotFoundHttpException
     */
    protected function findModel($ail_iata, $ail_lang): AirportLang
    {
        if (($model = AirportLang::findOne(['ail_iata' => $ail_iata, 'ail_lang' => $ail_lang])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The AirportLang does not exist.');
    }

    public function actionSynchronization(): \yii\web\Response
    {
        $timeStart = microtime(true);
        $lastUpdated = Yii::$app->cache->get(AirportLangService::CACHE_KEY);
        $info = AirportLangService::getInfo();

        if ($info['error']) {
            Yii::$app->getSession()->setFlash('error', $info['error']);
            return $this->redirect(['index']);
        }

        if ((int) $info['lastModified'] === (int) $lastUpdated) {
            $infoMessage = 'Last Modified Data not changed. (timestamp: ' .
                $info['lastModified'] . ', dateTime: ' . date('Y-m-d H:i:s', (int) $info['lastModified']) . ')';
            Yii::$app->getSession()->setFlash('info', $infoMessage);
            return $this->redirect(['index']);
        }

        set_time_limit(300);
        Yii::$app->log->targets['debug']->enabled = false;
        $error = $created = $updated = $errored = [];
        $processed = $disabled = 0;

        for ($pageIndex = 0; $pageIndex <= $info['allPages']; $pageIndex++) {
            $response = AirportLangService::synchronization(0, AirportLangService::PAGE_LIMIT, $pageIndex);

            if ($response['error']) {
                Yii::$app->getSession()->setFlash('error', $response['error']);
                $error[] = $response['error'];
            } else {
                $created = ArrayHelper::merge($created, $response['created']);
                $updated = ArrayHelper::merge($updated, $response['updated']);
                $errored = ArrayHelper::merge($errored, $response['errored']);
                $processed += $response['processed'];
                $disabled += $response['disabled'];
            }
        }

        Yii::$app->cache->set(AirportLangService::CACHE_KEY, $info['lastModified'], AirportLangService::CACHE_DURATION);

        if ($error || count($errored)) {
            $errorMessage = 'Errored: (' . count($errored) . ') ' . implode(', ', $errored) . '<br />';
            $errorMessage .= 'Errors: ' .  implode(', ', $error);
            Yii::$app->getSession()->setFlash('error', $errorMessage);
        }

        $successMessage = 'Created: (' . count($created) . ') ' . implode(', ', $created) . '<br />';
        $successMessage .= 'Updated: (' . count($updated) . ') ' . implode(', ', $updated);
        Yii::$app->getSession()->setFlash('success', $successMessage);

        $timeEnd = microtime(true);
        $executeTimeSeconds = number_format(round($timeEnd - $timeStart, 2), 2);

        $infoMessage = 'Total Iata: (' . $info['total'] . ') <br />';
        $infoMessage .= 'Processed: (' .  $processed . ') <br />';
        $infoMessage .= 'Disabled: (' .  $disabled . ') <br />';
        $infoMessage .= 'Execute Time: ' .  $executeTimeSeconds . ' sec <br />';
        $infoMessage .= 'Last Modified Data: (timestamp: ' .
                $info['lastModified'] . ', dateTime: ' . date('Y-m-d H:i:s', (int) $info['lastModified']) . ')';
        Yii::$app->getSession()->setFlash('info', $infoMessage);

        return $this->redirect(['index']);
    }
}
