<?php

namespace frontend\controllers;

use Yii;
use sales\entities\cases\CaseStatusLogSearch;

/**
 * Class CaseStatusLogController
 */
class CaseStatusLogController extends FController
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CaseStatusLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
