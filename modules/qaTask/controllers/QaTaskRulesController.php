<?php

namespace modules\qaTask\controllers;

use frontend\controllers\FController;
use sales\auth\Auth;
use Yii;
use modules\qaTask\src\entities\qaTaskRules\QaTaskRules;
use modules\qaTask\src\entities\qaTaskRules\search\QaTaskRulesSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class QaTaskRulesController
 */
class QaTaskRulesController extends FController
{
    public function actionIndex(): string
    {
        $searchModel = new QaTaskRulesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->tr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return QaTaskRules
     * @throws NotFoundHttpException
     */
    protected function findModel($id): QaTaskRules
    {
        if (($model = QaTaskRules::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
