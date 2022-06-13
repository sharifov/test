<?php

declare(strict_types=1);

namespace common\components\experimentManager;

use common\components\experimentManager\models\Experiment;
use common\components\experimentManager\models\ExperimentTarget;
use common\components\experimentMap\ExperimentMap;
use common\components\helpers\GeneralHelper;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

/**
 * Allows you to maintain experiments management
 *
 */
class ExperimentManager extends Component
{
    /**
     * Google Optimize storage
     */
    public const STORAGE_GOOGLE_OPTIMIZE = 'google_optimize';
    /**
     * @var array|string[]
     */
    public static array $defaultStorages = [
        self::STORAGE_GOOGLE_OPTIMIZE => [
            'class' => Experiment::class
        ]
    ];
    /**
     * @var bool
     */
    public bool $isActive = false;
    /**
     * @var string $migrationPath
     */
    public string $migrationPath;
    /**
     * @return ExperimentManager|null
     */
    public static function getInstance(): ?ExperimentManager
    {
        $module = GeneralHelper::getMainModule();
        if ($module && $module->hasComponent('experimentManager') && $module->experimentManager->isActive) {
            return $module->experimentManager;
        }
        return null;
    }
    /**
     * @param string $class
     * @param int $targetId
     * @param array $additionalExperiments
     * @return void
     */
    public function saveExperimentList(string $class, int $targetId, array $additionalExperiments = []): void
    {
        $activeExperiments = array_unique(array_merge($this->getActiveExperiments(), $additionalExperiments));
        foreach ($activeExperiments as $activeExperiment) {
            $this->saveExperiment($class, $targetId, $activeExperiment);
        }
    }
    /**
     * @param string $class
     * @param int $targetId
     * @param string $experimentCode
     * @return void
     */
    public function saveExperiment(string $class, int $targetId, string $experimentCode): void
    {
        $experimentRecord = Experiment::getExperimentByCode($experimentCode);
        if (empty($experimentRecord)) {
            $experimentRecord = new Experiment(['code' => $experimentCode]);
            $experimentRecord->save();
        }
        $experimentRecord->addTarget($class, $targetId);
    }
    /**
     * @param string $code
     * @return StorageInterface
     * @throws InvalidArgumentException|InvalidConfigException
     */
    private function getDefaultStorageByCode(string $code): StorageInterface
    {
        if (empty($storage = self::$defaultStorages[$code] ?? null)) {
            throw new InvalidArgumentException('Invalid storage code provided');
        }
        if (empty($storage['class'])) {
            throw new InvalidConfigException('Invalid default storage config');
        }
        return new $storage['class']();
    }
}
