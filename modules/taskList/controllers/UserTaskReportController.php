<?php

namespace modules\taskList\controllers;

use frontend\controllers\FController;
use modules\taskList\src\entities\userTask\UserTaskSearch;
use Yii;

/**
 * Class UserTaskReportController
 */
class UserTaskReportController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionIndex()
    {
        $searchModel = new UserTaskSearch();
        $query = $searchModel->queryReportSummary($this->request->queryParams);

        // $x = $query->asArray()->all(); /* TODO:: FOR DEBUG:: must by remove  */

        return $this->render('index', [
            'searchModel' => $searchModel,
            'result' => $query->asArray()->all(),
        ]);
    }

    public function actionIndexOld()
    {
        $searchModel = new UserTaskSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index1', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
