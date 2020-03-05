<?php

namespace frontend\controllers;

use sales\auth\Auth;
use sales\entities\cases\CasesQSearch;
use Yii;

/**
 * Class CasesQController
 */
class CasesQController extends FController
{

    public function actionPending()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchPending(Yii::$app->request->queryParams, Auth::user());

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionInbox()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchInbox(Yii::$app->request->queryParams, Auth::user());

        return $this->render('inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionFollowUp()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchFollowUp(Yii::$app->request->queryParams, Auth::user());

        return $this->render('follow-up', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionProcessing()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchProcessing(Yii::$app->request->queryParams, Auth::user());

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionSolved()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchSolved(Yii::$app->request->queryParams, Auth::user());

        return $this->render('solved', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTrash()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams, Auth::user());

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
