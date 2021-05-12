<?php

namespace modules\order\controllers;

use common\models\Transaction;
use frontend\controllers\FController;
use modules\order\src\transaction\repository\TransactionRepository;
use sales\helpers\app\AppHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class TransactionActionsController
 *
 * @property TransactionRepository $transactionRepository
 */
class TransactionActionsController extends FController
{
    private TransactionRepository $transactionRepository;

    /**
     * @param $id
     * @param $module
     * @param TransactionRepository $transactionRepository
     * @param array $config
     */
    public function __construct($id, $module, TransactionRepository $transactionRepository, $config = [])
    {
        $this->transactionRepository = $transactionRepository;
        parent::__construct($id, $module, $config);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionUpdate()
    {
        $transaction = $this->findModel((int) \Yii::$app->request->get('id'));

        if ($transaction->load(\Yii::$app->request->post()) && $transaction->validate()) {
            $this->transactionRepository->save($transaction);
            return '<script>
                $("#modal-df").modal("hide"); 
                if ($("#pjax-order-transaction-' . $transaction->trPayment->pay_order_id . '").length) {
                    $.pjax.reload({container: "#pjax-order-transaction-' . $transaction->trPayment->pay_order_id . '"});
                }
            </script>';
        }

        return $this->renderAjax('update', [
            'model' => $transaction,
        ]);
    }

    public function actionDelete()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        $result = ['message' => '', 'status' => 0];
        try {
            $transaction = $this->findModel((int) \Yii::$app->request->post('id'));
            $this->transactionRepository->remove($transaction);
            $result['status'] = 1;
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'TransactionActionsController:actionDelete:Throwable');
            $result['message'] = $throwable->getMessage();
        }
        return $this->asJson($result);
    }

    /**
     * @param $id
     * @return Transaction
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Transaction
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Transaction not found ID(' . $id . ')');
    }
}
