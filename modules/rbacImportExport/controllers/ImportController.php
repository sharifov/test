<?php
namespace modules\rbacImportExport\controllers;

use modules\rbacImportExport\src\forms\ImportForm;
use modules\rbacImportExport\src\traits\ModuleTrait;
use modules\rbacImportExport\src\useCase\import\ImportService;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * Class ImportController
 * @package modules\rbacImportExport\controllers
 *
 * @property ImportService $importService
 */
class ImportController extends Controller
{
	use ModuleTrait;

	/**
	 * @var ImportService
	 */
	private $importService;

	public function __construct($id, $module, ImportService $importService, $config = [])
	{
		parent::__construct($id, $module, $config);
		$this->importService = $importService;
	}

	public function actionView(): string
	{
		$form = new ImportForm();
		$form->zipFile = UploadedFile::getInstance($form, 'zipFile');

		$renderParams = [];

		if (\Yii::$app->request->isPost && $form->validate()) {
			try {
				$differences = $this->importService->getRbacDifference($form);

				$renderParams['rbacDifferences'] = $differences;

				$cacheKey = 'rbac-import-export_' . $this->importService->dataByteLength;
				if ($differences) {
					\Yii::$app->cache->set($cacheKey, $differences, $this->getModule()->params['cacheDuration']);
				}
				$renderParams['cacheKey'] = $cacheKey;
				$renderParams['fileSize'] = $this->importService->fileSize;
				$renderParams['fileName'] = $this->importService->generatedZipFileName;
				$renderParams['cacheDuration'] = $this->getModule()->params['cacheDuration'];
			} catch (\RuntimeException $e) {
				$form->addError('runtimeError', $e->getMessage());
			} catch (\Throwable | Exception $e) {
				\Yii::error($e->getMessage(), 'RbacImportExportModule::ImportController::actionImportView::Throwable');
				$form->addError('internalServerError', 'Internal server error');
			}
		}

		$renderParams['model'] = $form;

		return $this->render('_importView', $renderParams);
	}

	/**
	 * @throws BadRequestHttpException
	 */
	public function actionImportData()
	{
		if (\Yii::$app->request->isPost) {
			$cacheKey = \Yii::$app->request->post('cacheKey');
			$fileName = \Yii::$app->request->post('fileName');
			$fileSize = \Yii::$app->request->post('fileSize');

			if (empty($cacheKey)) {
				throw new BadRequestHttpException();
			}

			try {
				$resultJson = [
					'error' => false,
					'message' => 'RBAC data successfully imported',
				];

				$rbacData = \Yii::$app->cache->get($cacheKey);

				if (!$rbacData) {
					throw new \DomainException('Not found RBAC data in cache. Upload data again.');
				}

				if (empty($fileName) || empty($fileSize)) {
					throw new \DomainException('File info is not provided');
				}

				$this->importService->import($rbacData, (int)$fileSize);

				\Yii::$app->cache->delete($cacheKey);
				\Yii::$app->session->setFlash('success', 'RBAC data successfully imported');

				return $this->redirect('/rbac-import-export/log/index');
			} catch (\DomainException | \RuntimeException $e) {
				$resultJson['error'] = true;
				$resultJson['message'] = 'Error: ' . $e->getMessage()  . ' in File: ' . $e->getFile() . ' on Line: ' . $e->getLine();
			} catch (\Throwable | Exception $e) {
				\Yii::error($e->getMessage() . ' in File: ' . $e->getFile() . ' on Line: ' . $e->getLine(), 'RbacImportExportModule::ImportController::actionImportData::Throwable');
				$resultJson['error'] = true;
				$resultJson['message'] = 'Internal Server Error';
			}

			return $this->asJson($resultJson);
		}

		throw new BadRequestHttpException();
	}
}