<?php

namespace modules\product\controllers;

use modules\product\src\entities\productQuote\ProductQuote;
use frontend\controllers\FController;
use modules\product\src\entities\productQuoteStatusLog\search\ProductQuoteStatusLogSearch;
use sales\auth\Auth;
use Yii;
use yii\web\NotFoundHttpException;

class ProductQuoteStatusLogController extends FController
{
    public function actionShow(): string
    {
        $productQuote = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new ProductQuoteStatusLogSearch();

        $params = Yii::$app->request->queryParams;
        $params['ProductQuoteStatusLogSearch']['pqsl_product_quote_id'] = $productQuote->pq_id;

        $dataProvider = $searchModel->search($params, Auth::user());

        return $this->renderAjax('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($gid): ProductQuote
    {
        if (($model = ProductQuote::find()->andWhere(['pq_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
