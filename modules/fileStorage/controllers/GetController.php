<?php

namespace modules\fileStorage\controllers;

use common\models\Lead;
use frontend\controllers\FController;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\FileSystem;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class FileStorageGetController
 *
 * @property FileSystem $fileSystem
 */
class GetController extends FController
{
    private FileSystem $fileSystem;

    public function __construct($id, $module, FileSystem $fileSystem, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->fileSystem = $fileSystem;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'view',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            if (!FileStorageSettings::canDownload()) {
                throw new NotFoundHttpException();
            }
            return true;
        }
        return false;
    }

    public function actionView()
    {
        if (!Auth::can('file-storage/view')) {
            throw new ForbiddenHttpException('Access denied');
        }

        $form = new \modules\fileStorage\src\useCase\view\ViewForm();

        if (!$form->load(\Yii::$app->request->get()) || !$form->validate()) {
            return $this->asJson(['message' => 'Invalid request.']);
        }

        $file = FileStorage::find()->byUid($form->uid)->one();
        if (!$file) {
            throw new NotFoundHttpException();
        }

        if ($form->guard_enabled) {
            if ($form->isLead()) {
                $this->leadGuard($file->fs_id);
            }
            if ($form->isCase()) {
                $this->caseGuard($file->fs_id);
            }
        }

        try {
            if ($form->as_file) {
                \Yii::$app->response->sendContentAsFile($this->fileSystem->read($file->fs_path), $file->fs_name);
            } else {
                \Yii::$app->response->format = Response::FORMAT_RAW;
                \Yii::$app->response->headers->add('Content-Type', $file->fs_mime_type);
                \Yii::$app->response->stream = $this->fileSystem->readStream($file->fs_path);
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'View File storage error.',
                'error' => $e->getMessage(),
                'fileId' => $file->fs_id,
                'userId' => Auth::id(),
            ], 'FileStorage::Get::View');
            \Yii::$app->response->headers->add('Content-Type', 'text/html');
            return $this->asJson(['message' => 'Server error.']);
        }
    }

    private function leadGuard(int $fileId): void
    {
        $leadId = FileLead::find()->select(['fld_lead_id'])->byFile($fileId)->asArray()->one();
        if (!$leadId) {
            throw new NotFoundHttpException('Not found related Lead.');
        }
        $lead = Lead::findOne($leadId['fld_lead_id']);
        if (!$lead) {
            throw new NotFoundHttpException('Not found related Lead.');
        }
        //todo
//        if (!Auth::can('', ['lead' => $lead])) {
//            throw new ForbiddenHttpException('Access denied.');
//        }
    }

    private function caseGuard(int $fileId): void
    {
        $caseId = FileCase::find()->select(['fc_case_id'])->byFile($fileId)->asArray()->one();
        if (!$caseId) {
            throw new NotFoundHttpException('Not found related Case.');
        }
        $case = Cases::findOne($caseId['fc_case_id']);
        if (!$case) {
            throw new NotFoundHttpException('Not found related Case.');
        }
        //todo
//        if (!Auth::can('', ['case' => $case])) {
//            throw new ForbiddenHttpException('Access denied.');
//        }
    }
}
