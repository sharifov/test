<?php

namespace frontend\controllers;

use sales\entities\log\GlobalLogSearch;
use Yii;
use yii\web\BadRequestHttpException;

class GlobalLogController extends FController
{

	public function actionAjaxViewGeneralLeadLog(): string
	{
		if (Yii::$app->request->isAjax) {
			$leadId = Yii::$app->request->get('lid');

			$searchModel = new GlobalLogSearch();
			$params = Yii::$app->request->queryParams;
			$params['GlobalLogSearch']['leadId'] = $leadId;
			$dataProvider = $searchModel->searchByLead($params);

			return $this->renderAjax('partial/_general_lead_log', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'lid' => $leadId,
			]);
		}
		throw new BadRequestHttpException();
	}
}
