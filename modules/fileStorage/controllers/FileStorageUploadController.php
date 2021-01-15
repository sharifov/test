<?php

namespace modules\fileStorage\controllers;

use common\models\Lead;
use modules\fileStorage\src\LeadUploader;
use modules\fileStorage\src\useCase\uploadFile\UploadForm;
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
 */
class FileStorageUploadController extends Controller
{
    private LeadUploader $leadUploader;

    public function __construct($id, $module, LeadUploader $uploader, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadUploader = $uploader;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload-by-lead' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
}
