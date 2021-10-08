<?php

namespace sales\model\leadRedial\priorityLevel;

/**
 * Class SettingsPriorityLevel
 *
 * @property array $settings
 * @property int $default
 */
class SettingsPriorityLevelCalculator implements PriorityLevelCalculator
{
    private array $settings;
    private int $default;

    public function __construct()
    {
        $settings = \Yii::$app->params['settings']['redial_priority_level'];
        $default = -1;
        if (array_key_exists('default', $settings)) {
            $default = (int)$settings['default'];
            unset($settings['default']);
        }
        $settings = array_map('intval', $settings);
        arsort($settings);
        $this->settings = $settings;
        $this->default = $default;
    }

    public function calculate(float $percent): int
    {
        foreach ($this->settings as $level => $conversionPercent) {
            if ($percent > $conversionPercent) {
                return $level;
            }
        }
        return $this->default;
    }
}
