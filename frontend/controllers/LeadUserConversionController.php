<?php

namespace frontend\controllers;

use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use sales\helpers\app\AppHelper;
use sales\model\lead\LeadCodeException;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;
use Yii;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;

/**
 * Class LeadUserConversionController
 */
class LeadUserConversionController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'delete',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionDelete(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0];
            try {
                if ((!$leadId = (int) Yii::$app->request->post('lead_id')) || (!$userId = (int) Yii::$app->request->post('user_id'))) {
                    throw new BadRequestHttpException('Invalid parameters', -LeadCodeException::LEAD_USER_CONVERSATION_NOT_PARAM);
                }

                $leadAbacDto = new LeadAbacDto(Lead::findOne($leadId), $userId);
                if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_DELETE)) {
                    throw new ForbiddenHttpException('Access denied.', -LeadCodeException::LEAD_ACCESS_DENIED);
                }

                if (!$leadUserConversion = LeadUserConversion::findOne(['luc_lead_id' => $leadId, 'luc_user_id' => $userId])) {
                    throw new NotFoundHttpException('LeadUserConversion not found', -LeadCodeException::LEAD_USER_CONVERSATION_NOT_FOUND);
                }

                (new LeadUserConversionRepository())->remove($leadUserConversion);

                $result = ['message' => 'Lead User Conversation removed', 'status' => 1];
            } catch (\Throwable $throwable) {
                AppHelper::throwableLogger(
                    $throwable,
                    'LeadUserConversionController:actionDelete:Throwable'
                );
                $result['message'] = VarDumper::dumpAsString($throwable->getMessage());
            }
            return $result;
        }
        throw new BadRequestHttpException();
    }
}
