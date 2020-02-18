<?php

namespace modules\qaTask\controllers;

use common\models\Lead;
use modules\qaTask\src\guard\QaTaskGuard;
use modules\qaTask\src\useCases\qaTask\multiple\create\QaTaskMultipleCreateForm;
use modules\qaTask\src\useCases\qaTask\multiple\create\QaTaskMultipleCreateService;
use Yii;
use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class QaTaskMultipleController
 *
 * @property QaTaskMultipleCreateService $multipleCreateService
 */
class QaTaskMultipleController extends FController
{
    private $multipleCreateService;

    public function __construct(
        $id,
        $module,
        QaTaskMultipleCreateService $multipleCreateService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->multipleCreateService = $multipleCreateService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'create-lead',
                    'create-lead-validate',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string|Response
     * @throws ForbiddenHttpException
     */
    public function actionCreateLead()
    {
        if (!Auth::can('leads/index_Create_QA_Tasks')) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new QaTaskMultipleCreateForm(
            QaTaskObjectType::LEAD,
            QaTaskCategoryQuery::getEnabledListByLead(),
            Auth::id(),
            ['ids' => Yii::$app->request->post('ids')]
        );

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            foreach (Lead::find()->select(['project_id'])->andWhere(['id' => $form->ids])->asArray()->all() as $project) {
                QaTaskGuard::guard($project['project_id'], Auth::id());
            }

            try {
                $messages = $this->multipleCreateService->create($form);
                return $this->asJson([
                    'success' => true,
                    'message' => count($messages) . ' rows affected.',
                    'text' => $this->multipleCreateService->formatMessages(...$messages),
                ]);
            } catch (\DomainException $e) {
                return $this->asJson([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'text' => '',
                ]);
            }

        }

        $form->convertIdsToString();

        return $this->renderAjax('create', [
            'model' => $form,
            'summaryIdentifier' => '.multiple-update-summary .card-body',
            'modalId' => 'modal-df',
            'pjaxId' => 'lead-pjax-list',
            'script' => "$('.multiple-update-summary').slideDown();",
            'actionUrl' => ['/qa-task/qa-task-multiple/create-lead'],
            'validationUrl' => ['/qa-task/qa-task-multiple/create-lead-validate'],
        ]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function actionCreateLeadValidate(): array
    {
        if (!Auth::can('leads/index_Create_QA_Tasks')) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new QaTaskMultipleCreateForm(
            QaTaskObjectType::LEAD,
            QaTaskCategoryQuery::getEnabledListByLead(),
            Auth::id(),
            ['ids' => Yii::$app->request->post('ids')]
        );

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        throw new BadRequestHttpException();
    }
}
