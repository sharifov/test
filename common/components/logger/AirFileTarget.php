<?php
/**
 * Created by Alexandr.
 * User: alexandr
 * Date: 2/8/19
 * Time: 6:52 PM
 */

namespace common\components\logger;

use index0h\log\base\TargetTrait;
use Yii;

/**
 *
 * @property mixed $contextMessage
 */
class AirFileTarget extends \yii\log\FileTarget
{
    use TargetTrait;

    private $serviceName = 'project';
    private $serviceVersion = '1.0.0';

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->serviceName = Yii::$app->params['serviceName'];
        $this->serviceVersion = Yii::$app->params['serviceVersion'];
    }

    /**
     * @return array|string
     */
    protected function getContextMessage()
    {
        $context = $this->context;
        $context['service.name'] = $this->serviceName;
        $context['service.version'] = $this->serviceVersion;
        return $context;
    }
}