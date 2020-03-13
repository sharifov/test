<?php

namespace modules\rbacImportExport;

use Yii;
use yii\base\Module;

/**
 * rbac-import-export module definition class
 */
class RbacImportExportModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\rbacImportExport\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

		$this->setViewPath('@modules/rbacImportExport/views');

		Yii::configure($this, [
			'params' => [
				'tmpDir' => __DIR__ . '/src/tmp',
				'cacheDuration' => 600,
				'maxFileSizeUpload' => 1024 * 1024 * 2
			]
		]);
	}

	/**
	 * @param string $category
	 * @param string $message
	 * @param array $params
	 * @param null|string $language
	 * @return string
	 */
	public static function t($category, $message, $params = [], $language = null): string
	{
		return Yii::t('modules/rbacImportExport/' . $category, $message, $params, $language);
	}
}
