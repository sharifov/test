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
 *
 * @property mixed $appVersion
 * @property mixed $gitBranch
 * @property mixed $gitHash
 */
class AirFileTarget extends \yii\log\FileTarget
{
    use TargetTrait;

    private $serviceName = 'project';
    private $serviceVersion = '1.0.0';

    private $appVersion = '';
    private $gitBranch = '';
    private $gitHash = '';

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->serviceName = Yii::$app->params['serviceName'] ?? '';
        $this->serviceVersion = Yii::$app->params['serviceVersion'] ?? '';
        $this->appVersion = Yii::$app->params['release']['version'] ?? '';
        $this->gitBranch = Yii::$app->params['release']['git_branch'] ?? '';
        $this->gitHash = Yii::$app->params['release']['git_hash'] ?? '';
    }

    /**
     * @return array|string
     */
    protected function getContextMessage()
    {
//        if (is_array($this->context)) {
//            $context = $this->context;
//        }
        $context = $this->context;
        $context['service.name'] = $this->serviceName;
        $context['service.version'] = $this->serviceVersion;

        //$context['log.level'] = $this->;

        $prefix = $this->getMessagePrefix($this->context);
        if ($prefix && is_array($prefix)) {
            foreach ($prefix as $pkey => $pval) {
                $context['app.' . $pkey] = $pval;
            }
        }

        $context['app.version'] = $this->appVersion;
        $context['app.name'] = Yii::$app->name;
        $context['app.env'] = YII_ENV;
        $context['git.branch'] = $this->gitBranch;
        $context['git.hash'] = $this->gitHash;


        return $context;
    }
}
