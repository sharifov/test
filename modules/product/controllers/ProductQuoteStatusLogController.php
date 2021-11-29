<?php

namespace modules\product\controllers;

use modules\product\src\entities\productQuote\ProductQuote;
use frontend\controllers\FController;
use modules\product\src\entities\productQuoteStatusLog\search\ProductQuoteStatusLogSearch;
use sales\auth\Auth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ProductQuoteStatusLogController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'show',
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionShow(): string
    {
        $productQuote = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new ProductQuoteStatusLogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user(), $productQuote->pq_id);

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
