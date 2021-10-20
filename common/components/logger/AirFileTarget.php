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

    private $serviceName        = 'project';
    private $serviceVersion     = '1.0.0';
    private $serviceEndpoint    = 'frontend';
    private $serviceApp         = 'yii2';

    private $appVersion     = '';
    private $gitBranch      = '';
    private $gitHash        = '';

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->serviceName = Yii::$app->params['serviceName'] ?? '';
        $this->serviceVersion = Yii::$app->params['serviceVersion'] ?? '';
        $this->serviceEndpoint = Yii::$app->params['serviceEndpoint'] ?? '';
        $this->serviceApp = Yii::$app->params['serviceApp'] ?? '';

        $this->appVersion = Yii::$app->params['release']['version'] ?? '';
        $this->gitBranch = Yii::$app->params['release']['git_branch'] ?? '';
        $this->gitHash = Yii::$app->params['release']['git_hash'] ?? '';
    }

    /**
     * @return array
     */
    protected function getContextMessage(): array
    {
//        if (is_array($this->context)) {
//            $context = $this->context;
//        }
        $context = $this->context;
        $context['srv.name'] = $this->serviceName;
        $context['srv.ver'] = $this->serviceVersion;
        $context['srv.ept'] = $this->serviceEndpoint;
        $context['srv.app'] = $this->serviceApp;

        //$context['log.level'] = $this->;

        $prefix = $this->getMessagePrefix($this->context);
        if ($prefix && is_array($prefix)) {
            foreach ($prefix as $pkey => $pval) {
                $context['app.' . $pkey] = $pval;
            }
        }

        $context['app.ver']         = $this->appVersion;
        $context['app.name']        = Yii::$app->name;
        $context['app.env']         = YII_ENV;
        $context['git.branch']      = $this->gitBranch;
        $context['git.hash']        = $this->gitHash;

        return $context;
    }
}
