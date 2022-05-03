<?php

namespace modules\requestControl;

use yii\base\Module;
use yii\db\Query;
use modules\requestControl\models\UserSiteActivity;
use modules\requestControl\accessCheck\AdmissionPass;
use modules\requestControl\accessCheck\conditions\RoleCondition;
use modules\requestControl\accessCheck\conditions\UsernameCondition;

/**
 * Class RequestControlModule
 * @package modules\requestControl\controllers
 *
 * Entry point of module.
 *
 * More: https://www.yiiframework.com/doc/guide/2.0/ru/structure-modules
 */
class RequestControlModule extends Module
{
    const REQUEST_CONTROL_RULES_CACHE_KEY = 'requestControlRules';

    /**
     * @var string namespace of this module controllers
     */
    public $controllerNamespace = 'modules\requestControl\controllers';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        $this->setViewPath('@modules/requestControl/views');
    }

    /**
     * Function checks that specific entity have
     *
     * More details in phpdoc of: modules\requestControl\accessCheck\AdmissionPass
     *
     * @param AdmissionPass $admissionPass
     * @return bool
     */
    public function can(AdmissionPass $admissionPass): bool
    {
        $allowance = $admissionPass->createAllowance();
        return $admissionPass->checkAllowance($allowance);
    }

    /**
     * this function finds all exist user activities in database
     *
     * return value looks like:
     *
     * ```php
     * [
     *      "local" => "73",
     *      "global" => "37"
     * ]
     * ```
     *
     * @param int $userId
     * @param string $path
     * @param string $limitDate
     * @return array
     */
    public static function existActivities(int $userId, string $path, string $limitDate): array
    {
        $globalQuery = (new Query())
            ->from(UserSiteActivity::tableName())
            ->where(
                'usa_created_dt >= :limitDate AND usa_user_id=:userId',
                [":limitDate" => $limitDate, ":userId" => $userId]
            )
            ->select('COUNT(*) as global');

        $localQuery = (new Query())
            ->from(UserSiteActivity::tableName())
            ->where(
                'usa_created_dt >= :limitDate AND usa_user_id=:userId',
                [":limitDate" => $limitDate, ":userId" => $userId]
            )
            ->select('COUNT(*) as local')->andWhere('usa_page_url=:url', [":url" => $path]);

        return (new Query())
            ->select('*')
            ->from([
                'global' => $globalQuery,
                'local' => $localQuery
            ])->one();
    }

    /**
     * Returns list of available rule types
     *
     * @return array
     */
    public function ruleTypeList(): array
    {
        return [
            RoleCondition::TYPE => RoleCondition::TYPE,
            UsernameCondition::TYPE => UsernameCondition::TYPE
        ];
    }
}
