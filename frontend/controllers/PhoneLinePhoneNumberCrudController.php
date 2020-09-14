<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber;
use sales\model\phoneLine\phoneLinePhoneNumber\entity\search\PhoneLinePhoneNumberSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class PhoneLinePhoneNumberCrudController extends FController
{
	/**
	 * @return array
	 */
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
        $searchModel = new PhoneLinePhoneNumberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $plpn_line_id
     * @param integer $plpn_pl_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($plpn_line_id, $plpn_pl_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($plpn_line_id, $plpn_pl_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneLinePhoneNumber();

        $model->plpn_created_user_id = Auth::id();
        $model->plpn_updated_user_id = Auth::id();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plpn_line_id' => $model->plpn_line_id, 'plpn_pl_id' => $model->plpn_pl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plpn_line_id
     * @param integer $plpn_pl_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($plpn_line_id, $plpn_pl_id)
    {
        $model = $this->findModel($plpn_line_id, $plpn_pl_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plpn_line_id' => $model->plpn_line_id, 'plpn_pl_id' => $model->plpn_pl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plpn_line_id
     * @param integer $plpn_pl_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($plpn_line_id, $plpn_pl_id): Response
    {
        $this->findModel($plpn_line_id, $plpn_pl_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $plpn_line_id
     * @param integer $plpn_pl_id
     * @return PhoneLinePhoneNumber
     * @throws NotFoundHttpException
     */
    protected function findModel($plpn_line_id, $plpn_pl_id): PhoneLinePhoneNumber
    {
        if (($model = PhoneLinePhoneNumber::findOne(['plpn_line_id' => $plpn_line_id, 'plpn_pl_id' => $plpn_pl_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
