<?php

/**
 * Created by Alexandr.
 * User: alex.connor
 * Date: 20/10/2021
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
class FilebeatTarget extends \yii\log\FileTarget
{
    use TargetTrait;

    private string $serviceName        = 'app';
    private string $serviceVersion     = '1.0.0';
    private string $serviceType        = 'frontend';

    private string $appVersion     = '';
    private string $gitBranch      = '';
    private string $gitHash        = '';

    public function init()
    {
        parent::init();
        $this->serviceName = Yii::$app->params['serviceName'] ?? '';
        $this->serviceVersion = Yii::$app->params['serviceVersion'] ?? '';
        $this->serviceType = Yii::$app->params['serviceType'] ?? '';

        $this->appVersion = Yii::$app->params['release']['version'] ?? '';
        $this->gitBranch = Yii::$app->params['release']['git_branch'] ?? '';
        $this->gitHash = Yii::$app->params['release']['git_hash'] ?? '';
    }

    /**
     * @return array
     */
    protected function getContextMessage(): array
    {
        $context = $this->context;
        $context['service.name'] = $this->serviceName;
        $context['service.type'] = $this->serviceType;
        $context['service.version'] = $this->appVersion; //$this->serviceVersion;
        $context['service.env'] = YII_ENV;
        $context['service.git_branch'] = str_replace('refs/heads/', '', $this->gitBranch);
        $context['service.git_hash'] = substr($this->gitHash, 7);

        $prefix = $this->getMessagePrefix($this->context);
        if ($prefix && is_array($prefix)) {
            foreach ($prefix as $pkey => $pval) {
                $context['app.' . $pkey] = $pval;
            }
        }

        return $context;
    }
}
