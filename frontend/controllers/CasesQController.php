<?php

namespace frontend\controllers;

use sales\entities\cases\CasesQSearch;
use Yii;
use yii\web\Controller;

/**
 * Class CasesQController
 */
class CasesQController extends FController
{

    public function actionPending()
    {
        $isAgent = $this->isUserAgent();

        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchPending(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionInbox()
    {
        $isAgent = $this->isUserAgent();

        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchInbox(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionFollowUp()
    {
        $isAgent = $this->isUserAgent();

        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchFollowUp(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('follow-up', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionProcessing()
    {
        $isAgent = $this->isUserAgent();

        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchProcessing(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionSolved()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchSolved(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('solved', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTrash()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams, Yii::$app->user->identity);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return bool
     */
    private function isUserAgent(): bool
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        return $user->isAgent();
    }

}
