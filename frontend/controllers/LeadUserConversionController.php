<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\model\lead\LeadCodeException;
use src\model\leadUserConversion\form\LeadUserConversionAddForm;
use src\model\leadUserConversion\repository\LeadUserConversionRepository;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use Yii;
use src\model\leadUserConversion\entity\LeadUserConversion;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use common\components\bootstrap4\activeForm\ActiveForm;

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
                    'delete', 'add'
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionAdd()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }
        if (!$leadId = (int) Yii::$app->request->get('lead_id')) {
            throw new BadRequestHttpException('Invalid parameters');
        }
        $leadAbacDto = new LeadAbacDto(Lead::findOne($leadId), Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new LeadUserConversionAddForm($leadId);
        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $leadUserConversionRepository = new LeadUserConversionRepository();
                try {
                    if ($leadUserConversionRepository->exist($form->leadId, $form->userId)) {
                        return $this->asJson(['success' => false, 'message' => 'User conversion for this lead and user is already exist.']);
                    }
                    $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                    $isAdded = $leadUserConversionService->addManual(
                        $form->leadId,
                        $form->userId,
                        LeadUserConversionDictionary::DESCRIPTION_QA,
                        Auth::id()
                    );
                    if ($isAdded) {
                        return $this->asJson(['success' => true]);
                    }
                    return $this->asJson(['success' => false, 'message' => 'Not possible to add user conversion.']);
                } catch (\DomainException $e) {
                    return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
                } catch (\Throwable $e) {
                    Yii::error($e, 'LeadUserConversionController:actionAdd');
                    return $this->asJson(['success' => false, 'message' => 'Server error']);
                }
            }
            return $this->asJson(ActiveForm::formatError($form));
        }

        $userList = Employee::getActiveUsersList();
//        $userList = Employee::getActiveUsersListFromCommonGroups(Auth::id());
//        $usersExist = LeadUserConversionService::getUserIdsByLead($leadId);
//        $userList = array_diff_key($userList, $usersExist);

        return $this->renderAjax('add', [
            'leadUserConversionAddForm' => $form,
            'leadId' => $leadId,
            'userList' => $userList,
        ]);
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

                $leadAbacDto = new LeadAbacDto(Lead::findOne($leadId), Auth::id());
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
