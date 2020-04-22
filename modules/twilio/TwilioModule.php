<?php

namespace modules\twilio;

use modules\twilio\components\TwilioCommunicationService;

/**
 * Twilio module definition class
 *
 * @property TwilioCommunicationService $twilioCommunicationService
 */
class TwilioModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\twilio\controllers';

    public $viewPath = '@modules/twilio/views';

	public static function t($category, $message, $params = [], $language = null): string
	{
		return \Yii::t('modules/twilio/' . $category, $message, $params, $language);
	}
}
