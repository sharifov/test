<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Language;
use sales\helpers\app\AppHelper;
use sales\model\userVoiceMail\useCase\manage\UserVoiceMailForm;
use Yii;
use sales\model\userVoiceMail\entity\UserVoiceMail;
use sales\model\userVoiceMail\entity\search\UserVoiceMailSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class UserVoiceMailController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserVoiceMailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserVoiceMailForm();
        $model->uvm_max_recording_time = 60;
        $model->uvm_say_voice = 'alice';

        if ($model->load(Yii::$app->request->post())) {
			$model->recordFile = UploadedFile::getInstance($model, 'recordFile');
			try {
				if($model->validate() && $model->save(false)) {
					return $this->redirect(['view', 'id' => $model->uvm_id]);
				}
			} catch (\Throwable $e) {
				AppHelper::throwableLogger($e, 'UserVoiceMail::save');
				$model->addError('general', 'Internal Server Error');
				$model->deleteRecord();
			}
        }

        $languages = Language::getList();
        $model->uvm_say_language = in_array('en-US', $languages, false) ? 'en-US' : null;

        return $this->render('create', [
            'model' => $model,
			'languageList' => $languages
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = UserVoiceMailForm::findOne($id);

        if ($model) {
			if ($model->load(Yii::$app->request->post())) {
				$model->recordFile = UploadedFile::getInstance($model, 'recordFile');
				try {
					if($model->validate() && $model->updateRow()) {
						return $this->redirect(['view', 'id' => $model->uvm_id]);
					}
				} catch (\Throwable $e) {
					AppHelper::throwableLogger($e, 'UserVoiceMail::save');
					$model->addError('general', 'Internal Server Error');
				}
			}
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		$languages = Language::getList();
		return $this->render('update', [
            'model' => $model,
			'languageList' => $languages
		]);
    }

    /**
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**s
	 * @return string
	 * @throws BadRequestHttpException
	 */
    public function actionAjaxCreate(): string
	{
		$userId = Yii::$app->request->get('uid');
		if (!$user = Employee::findOne($userId)) {
			throw new BadRequestHttpException('Invalid User Id: ' . $userId, 1);
		}

		$userVoiceMail = new UserVoiceMailForm();
		$userVoiceMail->uvm_user_id = $userId;
		$userVoiceMail->uvm_max_recording_time = 10;

		if ($userVoiceMail->load(Yii::$app->request->post())) {
			$userVoiceMail->recordFile = UploadedFile::getInstance($userVoiceMail, 'recordFile');
			try {
				if($userVoiceMail->validate() && $userVoiceMail->save(false)) {
					return 'Success <script>$("#modal-df").modal("hide"); createNotify("Success", "Voice Mail created successfully", "success");</script>';
				}
			} catch (\Throwable $e) {
				AppHelper::throwableLogger($e, 'UserVoiceMail::save');
				$userVoiceMail->addError('general', 'Internal Server Error');
				$userVoiceMail->deleteRecord();
			}
		}

		return $this->renderAjax('create_ajax', [
			'model' => $userVoiceMail,
		]);
	}

	public function actionAjaxUpdate(): string
	{
		$id = Yii::$app->request->get('id');

		$model = UserVoiceMailForm::findOne($id);

		if ($model) {

			if ($model->load(Yii::$app->request->post())) {
				$model->recordFile = UploadedFile::getInstance($model, 'recordFile');
				try {
					if($model->validate() && $model->updateRow()) {
						return 'Success <script>$("#modal-df").modal("hide"); createNotify("Success", "Voice Mail updated successfully", "success");</script>';
					}
				} catch (\Throwable $e) {
					AppHelper::throwableLogger($e, 'UserVoiceMail::save');
					$model->addError('general', 'Internal Server Error');
				}
			}

		} else {
			$model->addError('general', 'User Voice Mail entity not found');
		}
		return $this->renderAjax('update_ajax', [
			'model' => $model,
		]);
	}

	/**
	 * @return string
	 * @throws BadRequestHttpException
	 * @throws NotFoundHttpException
	 * @throws StaleObjectException
	 * @throws \Throwable
	 */
	public function actionAjaxDelete()
	{
		$id = Yii::$app->request->get('id');

		$model = $this->findModel($id);

		if ($model->delete()) {
			return $this->asJson(['error' => false]);
		}

		return $this->asJson(['error' => true, 'message' => $model->getErrorSummary(false)[0]]);
	}

    /**
     * @param integer $id
     * @return UserVoiceMail
     * @throws NotFoundHttpException
     */
    protected function findModel($id): UserVoiceMail
    {
        if (($model = UserVoiceMail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
