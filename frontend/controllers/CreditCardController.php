<?php

namespace frontend\controllers;

use common\models\SaleCreditCard;
use frontend\models\form\CreditCardForm;
use Yii;
use common\models\CreditCard;
use common\models\search\CreditCardSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CreditCardController implements the CRUD actions for CreditCard model.
 */
class CreditCardController extends FController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all CreditCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CreditCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CreditCard model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CreditCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreditCardForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelCc = new CreditCard();
            $modelCc->attributes = $model->attributes;
            $modelCc->updateSecureCardNumber();
            $modelCc->updateSecureCvv();

            if ($modelCc->save()) {
                return $this->redirect(['view', 'id' => $modelCc->cc_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CreditCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $modelCc = $this->findModel($id);
        $model = new CreditCardForm();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $modelCc->attributes = $model->attributes;

                $modelCc->cc_expiration_month = $model->cc_expiration_month;
                $modelCc->cc_expiration_year = $model->cc_expiration_year;

                $modelCc->updateSecureCardNumber();
                $modelCc->updateSecureCvv();
                if ($modelCc->save()) {
                    return $this->redirect(['view', 'id' => $modelCc->cc_id]);
                } else {
                    Yii::error(VarDumper::dumpAsString($modelCc->errors), 'CreditCard:Update:save');
                }
            }
        } else {
            $model->attributes = $modelCc->attributes;
            $model->cc_number = $modelCc->initNumber;
            $model->cc_cvv = $modelCc->initCvv;
            $model->cc_expiration = date('m / y', strtotime($modelCc->cc_expiration_year.'-'.$modelCc->cc_expiration_month.'-01'));


        }

        $model->cc_id = $modelCc->cc_id;

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAjaxAddCreditCard()
	{
		$caseId = Yii::$app->request->get('caseId');
		$saleId = Yii::$app->request->get('saleId');

		if (!$caseId || !$saleId) {
			throw new BadRequestHttpException();
		}

		try {
			$form = new CreditCardForm();

			if ($form->load(Yii::$app->request->post()) && $form->validate()) {
				$model = new CreditCard();
				$model->attributes = $form->attributes;
				$model->cc_status_id = CreditCard::STATUS_VALID;
				$model->updateSecureCardNumber();
				$model->updateSecureCvv();

				if ($model->save()) {

					$saleCreditCard = new SaleCreditCard();
					$saleCreditCard->scc_sale_id = $saleId;
					$saleCreditCard->scc_cc_id = $model->primaryKey;

					if (!$saleCreditCard->save()) {
						throw new \RuntimeException($saleCreditCard->getErrorSummary(false)[0]);
					} else {
						return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-credit-card-table"}); createNotify("Success", "Credit Card Successfully created", "success")</script>';
					}
				}
			}
		} catch (\Throwable $e) {
			$form->addError('general', $e->getMessage());
		}

		return $this->renderAjax('_form', [
			'caseId' => $caseId,
			'saleId' => $saleId,
			'model' => $form,
			'isAjax' => true
		]);
	}

    /**
     * Deletes an existing CreditCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CreditCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CreditCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CreditCard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
