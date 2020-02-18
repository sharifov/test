<?php

namespace modules\qaTask\controllers;

use modules\qaTask\src\guard\QaTaskGuard;
use Yii;
use common\models\Lead;
use frontend\controllers\FController;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategoryQuery;
use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyForm;
use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyService;
use sales\auth\Auth;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class QaTaskCreateController
 *
 * @property QaTaskCreateManuallyService $createManuallyService
 */
class QaTaskCreateController extends FController
{
    private $createManuallyService;

    public function __construct(
        $id,
        $module,
        QaTaskCreateManuallyService $createManuallyService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->createManuallyService = $createManuallyService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'lead',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionLead()
    {
        if (!Auth::can('lead/view_QA_Tasks')) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $objectId = (int)Yii::$app->request->get('objectId');

        if (!$lead = Lead::findOne($objectId)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        QaTaskGuard::guard($lead->project_id, Auth::id());

        $form = new QaTaskCreateManuallyForm(
            QaTaskObjectType::LEAD,
            $lead->id,
            $lead->project_id,
            $lead->l_dep_id,
            QaTaskCategoryQuery::getEnabledListByLead(),
            Auth::id()
        );

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->createManuallyService->create($form);
                Yii::$app->session->addFlash('success', 'Qa Task created.');
            } catch (\DomainException $e) {
                Yii::$app->session->addFlash('error', $e->getMessage());
            }
            return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
        }

        return $this->renderAjax('lead', [
            'model' => $form
        ]);
    }
}
