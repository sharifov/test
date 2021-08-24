<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\abac\dto\OrderAbacDto;
use modules\order\src\abac\OrderAbacObject;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderStatusLog\search\OrderStatusLogSearch;
use sales\auth\Auth;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class OrderStatusLogController extends FController
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
        $order = $this->findModel((string)Yii::$app->request->get('gid'));

        $orderAbacDto = new OrderAbacDto($order);
        if (!Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_STATUS_LOG, OrderAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $searchModel = new OrderStatusLogSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user(), $order->or_id);

        return $this->renderAjax('show', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $gid
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($gid): Order
    {
        if (($model = Order::find()->andWhere(['or_gid' => $gid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
