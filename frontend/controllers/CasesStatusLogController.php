<?php

namespace frontend\controllers;

use Yii;
use sales\entities\cases\CasesStatusLogSearch;

/**
 * Class CasesStatusLogController
 */
class CasesStatusLogController extends FController
{

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CasesStatusLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
