<?php

namespace modules\fileStorage\controllers;

use common\models\Lead;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\CaseUploader;
use modules\fileStorage\src\LeadUploader;
use modules\fileStorage\src\useCase\uploadFile\UploadForm;
use sales\entities\cases\Cases;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class FileStorageUploadController
 *
 * @property LeadUploader $leadUploader
 * @property CaseUploader $caseUploader
 */
class FileStorageUploadController extends Controller
{
    private LeadUploader $leadUploader;
    private CaseUploader $caseUploader;

    public function __construct($id, $module, LeadUploader $leadUploader, CaseUploader $caseUploader, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadUploader = $leadUploader;
        $this->caseUploader = $caseUploader;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload-by-lead' => ['POST'],
                    'upload-by-case' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            if (!FileStorageSettings::isEnabled() || !FileStorageSettings::isUploadEnabled()) {
                throw new NotFoundHttpException();
            }
            return true;
        }
        return false;
    }

    public function actionUploadByLead()
    {
        $leadId = (int)\Yii::$app->request->get('id');
        if (!$leadId) {
            throw new BadRequestHttpException('Lead id is empty.');
        }
        $lead = Lead::findOne($leadId);
        if (!$lead) {
            throw new NotFoundHttpException('Lead is not found.');
        }

        $form = new UploadForm();
        $form->load(\Yii::$app->request->post());
        $form->file = UploadedFile::getInstance($form, 'file');

        if (!$form->validate()) {
            return $this->asJson([
                'error' => true,
                'errors' => $form->getErrors(),
                'message' => '',
            ]);
        }

        try {
            $this->leadUploader->upload(
                $lead->id,
                $lead->client_id,
                $lead->project->project_key,
                $form->fs_title,
                $form->file
            );
        } catch (\DomainException $e) {
            return $this->asJson([
                'error' => true,
                'errors' => [],
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Upload FileStorage byLead',
                'error' => $e->getMessage(),
            ], 'FileStorageUploadController:actionUploadByLead');
            return $this->asJson([
                'error' => true,
                'errors' => [],
                'message' => 'Server error.',
            ]);
        }

        return $this->asJson([
            'error' => false,
            'message' => 'OK',
        ]);
    }

    public function actionUploadByCase()
    {
        $caseId = (int)\Yii::$app->request->get('id');
        if (!$caseId) {
            throw new BadRequestHttpException('Case id is empty.');
        }
        $case = Cases::findOne($caseId);
        if (!$case) {
            throw new NotFoundHttpException('Case is not found.');
        }

        $form = new UploadForm();
        $form->load(\Yii::$app->request->post());
        $form->file = UploadedFile::getInstance($form, 'file');

        if (!$form->validate()) {
            return $this->asJson([
                'error' => true,
                'errors' => $form->getErrors(),
                'message' => '',
            ]);
        }

        try {
            $this->caseUploader->upload(
                $case->cs_id,
                $case->cs_client_id,
                $case->project->project_key,
                $form->fs_title,
                $form->file
            );
        } catch (\DomainException $e) {
            return $this->asJson([
                'error' => true,
                'errors' => [],
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Upload FileStorage byCase',
                'error' => $e->getMessage(),
            ], 'FileStorageUploadController:actionUploadByCase');
            return $this->asJson([
                'error' => true,
                'errors' => [],
                'message' => 'Server error.',
            ]);
        }

        return $this->asJson([
            'error' => false,
            'message' => 'OK',
        ]);
    }
}
