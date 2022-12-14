<?php

namespace frontend\controllers;

use src\auth\Auth;
use src\entities\cases\CaseCategory;
use src\entities\cases\CaseCategoryKeyDictionary;
use src\entities\cases\CasesQSearch;
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

    public function actionAwaiting()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchAwaiting(Yii::$app->request->queryParams, Auth::user());

        return $this->render('awaiting', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionAutoProcessing()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchAutoProcessing(Yii::$app->request->queryParams, Auth::user());

        return $this->render('auto-processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionError()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchError(Yii::$app->request->queryParams, Auth::user());

        return $this->render('error', [
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

    public function actionSecondPriority()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchSecondPriority(Yii::$app->request->queryParams, Auth::user());

        return $this->render('second-priority', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionPassDeparture()
    {
        $searchModel = new CasesQSearch();
        $dataProvider = $searchModel->searchPassDeparture(Yii::$app->request->queryParams, Auth::user());

        return $this->render('pass-departure', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }

    public function actionCrossSaleInbox(): string
    {
        $searchModel = new CasesQSearch();
        $categoryList = CaseCategory::getIdListByCategoryKeys([CaseCategoryKeyDictionary::CROSS_SALE]);
        $dataProvider = $searchModel->searchByCategory(Yii::$app->request->queryParams, Auth::user(), $categoryList);

        return $this->render('cross-sale-inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => Auth::user()->isAgent(),
        ]);
    }
}
