<?php

namespace modules\rbacImportExport\controllers;

use modules\rbacImportExport\src\forms\ExportForm;
use modules\rbacImportExport\src\traits\ModuleTrait;
use modules\rbacImportExport\src\useCase\export\ExportService;
use Yii;
use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\entity\search\AuthImportExportSearch;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExportController implements the CRUD actions for AuthImportExport model.
 *
 * @property ExportService $exportService
 */
class ExportController extends Controller
{
	use ModuleTrait;

	/**
	 * @var ExportService
	 */
	private $exportService;

	/**
	 * ExportController constructor.
	 * @param $id
	 * @param $module
	 * @param ExportService $exportService
	 * @param array $config
	 */
	public function __construct($id, $module, ExportService $exportService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->exportService = $exportService;
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
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    public function actionView()
	{
		$authManager = $this->authManager;
		$roles = $authManager->getRoles();

		$form = new ExportForm();
		$form->roles = array_column(ArrayHelper::toArray($roles), 'name', 'name');

		if ($form->load(Yii::$app->request->post()) && $form->validate()) {

			try {
				$export = $this->exportService->create($form);
				Yii::$app->session->setFlash('success', 'Data successfully exported.');

				return $this->redirect(['log/view', 'id' => $export->aie_id]);
			} catch (\Throwable $e) {
				\Yii::error($e->getMessage() . ' in File: ' . $e->getFile() . ' on Line: ' . $e->getLine(), 'ExportController::actionExportView::Throwable');
				$form->addError('error', 'Internal Server Error');
			}
		}

		return $this->render('_exportView', [
			'model' => $form
		]);
	}

    /**
     * Finds the AuthImportExport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthImportExport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthImportExport::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
