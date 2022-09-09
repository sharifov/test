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

    public function actionIndex(): string
    {
        $searchModel = new UserTaskSearch();
        $query = $searchModel->queryReportSummary($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'result' => $query->asArray()->one(),
        ]);
    }
}
