<?php

namespace modules\offer\controllers;

use frontend\controllers\FController;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerStatusLog\search\OfferStatusLogSearch;
use sales\auth\Auth;
use Yii;
use yii\web\NotFoundHttpException;

class OfferStatusLogController extends FController
{
    public function actionShow(): string
    {
        $offer = $this->findModel((string)Yii::$app->request->get('gid'));

        $searchModel = new OfferStatusLogSearch();

        $params = Yii::$app->request->queryParams;
        $params['OfferStatusLogSearch']['osl_offer_id'] = $offer->of_id;

        $dataProvider = $searchModel->search($params, Auth::user());

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
