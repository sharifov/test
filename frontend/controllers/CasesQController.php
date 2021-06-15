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

    public function actionNeedAction()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchNeedAction(Yii::$app->request->queryParams, Auth::user());

        return $this->render('need-action', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionUnidentified()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchUnidentified(Yii::$app->request->queryParams, Auth::user());

        return $this->render('unidentified', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionFirstPriority()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchFirstPriority(Yii::$app->request->queryParams, Auth::user());

        return $this->render('first-priority', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }
}
