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
	 * @var string
	 */
    public $tmpDir = '@runtime/';

	/**
	 * @var int
	 */
    public $cacheDuration = 600;

	/**
	 * @var float|int
	 */
    public $maxFileSizeUpload = 1024 * 1024 * 2;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

		$this->setViewPath('@modules/rbacImportExport/views');
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
