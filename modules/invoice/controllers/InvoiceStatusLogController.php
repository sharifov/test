<?php

namespace modules\invoice\controllers;

use frontend\controllers\FController;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoiceStatusLog\search\InvoiceStatusLogSearch;
use sales\auth\Auth;
use Yii;
use yii\web\NotFoundHttpException;

class InvoiceStatusLogController extends FController
{
    public function actionShow(): string
    {
        $invoice = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new InvoiceStatusLogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user(), $invoice->inv_id);

        return $this->renderAjax('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $gid
     * @return Invoice
     * @throws NotFoundHttpException
     */
    protected function findModel($gid): Invoice
    {
        if (($model = Invoice::find()->andWhere(['inv_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
