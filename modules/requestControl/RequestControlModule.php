<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 6:51 PM
 */

namespace modules\requestControl;

use frontend\models\UserSiteActivity;
use modules\requestControl\accessCheck\AdmissionPass;
use yii\base\Module;
use yii\db\Query;

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
        $query = (new Query())
            ->from(UserSiteActivity::tableName())
            ->where(
                'usa_created_dt >= :limitDate AND usa_user_id=:userId',
                [":limitDate" => $limitDate, ":userId" => $userId]
            );

        return (new Query())
            ->select('*')
            ->from([
                $query->select('COUNT(*) as global'),
                $query->select('COUNT(*) as local')->andWhere('usa_page_url=:url', [":url" => $path])
            ])
            ->one();
    }
}