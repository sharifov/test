<?php

namespace modules\rbacImportExport\controllers;

use modules\rbacImportExport\src\forms\ExportForm;
use modules\rbacImportExport\src\useCase\export\ExportService;
use Yii;
use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\entity\search\AuthImportExportSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ImportExportController implements the CRUD actions for AuthImportExport model.
 *
 * @property ExportService $exportService
 */
class ImportExportController extends FController
{
	/**
	 * @var ExportService
	 */
	private $exportService;

	/**
	 * ImportExportController constructor.
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

    /**
     * Lists all AuthImportExport models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthImportExportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthImportExport model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionExportView()
	{
		$authManager = Yii::$app->getAuthManager();
		$roles = $authManager->getRoles();

		$form = new ExportForm();
		$form->roles = array_column(ArrayHelper::toArray($roles), 'name', 'name');

		if ($form->load(Yii::$app->request->post()) && $form->validate()) {

			try {
				$export = $this->exportService->create($form, $authManager);
				Yii::$app->session->setFlash('success', 'Data successfully exported.');

				return $this->redirect(['import-export/view', 'id' => $export->aie_id]);
			} catch (\Throwable $e) {
				\Yii::error($e->getMessage() . ' in File: ' . $e->getFile() . ' on Line: ' . $e->getLine(), 'ImportExportController::actionExportView::Throwable');
				$form->addError('error', 'Internal Server Error');
			}
		}

		return $this->render('_exportView', [
			'model' => $form
		]);
	}

	/**
	 * @param $id
	 * @throws BadRequestHttpException
	 */
	public function actionDownload($id): void
	{
		try {
			$zip = $this->exportService->download((int)$id);
			Yii::$app->response->sendFile($zip);
			$this->exportService->removeFiles();
			return;
		} catch (\Throwable $e) {
			\Yii::error($e->getMessage() . ' in File: ' . $e->getFile() . ' on Line: ' . $e->getLine(), 'ImportExportController::actionExportView::Throwable');
		}
		throw new BadRequestHttpException();
	}

	/**
	 * @param $id
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 */
	public function actionDelete($id): \yii\web\Response
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
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
