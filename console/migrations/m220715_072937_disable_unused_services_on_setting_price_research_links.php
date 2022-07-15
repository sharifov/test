<?php

use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Json;

/**
 * Class m220715_072937_disable_unused_services_on_setting_price_research_links
 */
class m220715_072937_disable_unused_services_on_setting_price_research_links extends Migration
{
    private const SERVICE_KEY_MOMONDO = 'momondo';
    private const SERVICE_KEY_JUSTFLY = 'justfly_com';
    private const SERVICES = [
        self::SERVICE_KEY_MOMONDO,
        self::SERVICE_KEY_JUSTFLY
    ];

    private const SETTING_KEY = 'price_research_links';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->updateSettings(function (array $projectSetting) {
                $projectSetting['enabled'] = false;

                return $projectSetting;
            });
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220715_072937_disable_unused_services_on_setting_price_research_links:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->updateSettings(function (array $projectSetting) {
                $projectSetting['enabled'] = true;

                return $projectSetting;
            });
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220715_072937_disable_unused_services_on_setting_price_research_links:safeDown:Throwable'
            );
        }
    }

    private function findSetting(): array
    {
        $setting = (new Query())
            ->select('*')
            ->from('{{%setting}}')
            ->where(['s_key' => self::SETTING_KEY])
            ->one();

        if ($setting === false) {
            throw new \RuntimeException('Setting with key ' . self::SETTING_KEY . ' not found');
        }

        return $setting;
    }

    private function updateValue(array $value): void
    {
        $this->update(
            '{{%setting}}',
            ['s_value' => Json::encode($value)],
            ['s_key' => self::SETTING_KEY],
        );
    }

    private function updateSettings(callable $callback): void
    {
        $setting = $this->findSetting();
        $existsData = Json::decode($setting['s_value'], true);

        foreach ($existsData as $key => $project) {
            if (in_array($key, self::SERVICES)) {
                $existsData[$key] = $callback($project);
            }
        }

        $this->updateValue($existsData);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
