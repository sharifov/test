<?php

namespace modules\offer\controllers;

use frontend\controllers\FController;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerSendLog\search\OfferSendLogSearch;
use sales\auth\Auth;
use Yii;
use yii\web\NotFoundHttpException;

class OfferSendLogController extends FController
{
    public function actionShow(): string
    {
        $offer = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new OfferSendLogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user(), $offer->of_id);

        return $this->renderAjax('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $gid
     * @return Offer
     * @throws NotFoundHttpException
     */
    protected function findModel($gid): Offer
    {
        if (($model = Offer::find()->andWhere(['of_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
