<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 9:33 PM
 */

namespace modules\requestControl\accessCheck;


use yii\db\Query;

use modules\requestControl\interfaces\AllowanceInterface;
use modules\requestControl\interfaces\ConditionInterface;

use modules\requestControl\accessCheck\allowance\Limited;
use modules\requestControl\accessCheck\allowance\Limitless;
use modules\requestControl\accessCheck\conditions\RoleCondition;
use modules\requestControl\accessCheck\conditions\UsernameCondition;

use modules\requestControl\models\RequestControlRule;
use modules\requestControl\RequestControlModule;

/**
 * Class `AdmissionPass` basic class for working with access control
 * @package modules\requestControl\accessCheck
 *
 * This class implements all required business logic for module work.
 *
 * Usage example:
 *
 * ```php
 *  ...
 *  use modules\requestControl\accessCheck\AdmissionPass;
 *  use modules\requestControl\accessCheck\conditions\UsernameCondition;
 *  use modules\requestControl\accessCheck\conditions\RoleCondition;
 *  ...
 *  class MyClass {
 *      ...
 *      public function myCallFunction() {
 *          ...
 *          // Creating the checking entity for 600sec limit
 *          $checkAccess = new AdmissionPass(600);
 *          $checkAccess
 *              ->addCondition(UsernameCondition::TYPE, "johndoe")
 *              ->addCondition(RoleCondition::TYPE, ["admin", "agent"])
 *
 *          if (\Yii::$app->getModule('requestControl')->can($checkAccess) === true) {
 *              // ...code, if access allow
 *          } else {
 *              // ...code, if access deny
 *          }
 *          ...
 *      }
 *      ...
 *  }
 * ```
 */
class AdmissionPass
{
    /** @var ConditionInterface[] $conditions */
    private $conditions = [];

    /** @var RequestCountLedger $requestCountLedger */
    private $userActivityRegistry;

    /**
     * AdmissionPass constructor.
     * @param int $period
     */
    public function __construct(int $period)
    {
        $activityList = RequestControlModule::existActivities(
            \Yii::$app->user->id,
            \Yii::$app->request->pathInfo,
            date('Y-m-d H:i:s', time() - $period)
        );

        $this->conditions = [];
        $this->userActivityRegistry = new RequestCountLedger($activityList);
    }

    /**
     * Static function for new instance creation
     *
     * @param int $period
     * @return AdmissionPass
     */
    public static function create(int $period): self
    {
        return new self($period);
    }

    /**
     * @param string $type
     * @param string|array $value
     * @return AdmissionPass
     */
    public function addCondition(string $type, $value): self
    {
        switch ($type) {
            case UsernameCondition::TYPE:
                $this->conditions[] = new UsernameCondition($value);
                break;
            case RoleCondition::TYPE:
                $this->conditions[] = new RoleCondition($value);
                break;
        }
        return $this;
    }

    /**
     * @return AllowanceInterface
     */
    public function createAllowance(): AllowanceInterface
    {
        $query = (new Query())
            ->select('*')
            ->from(RequestControlRule::tableName());
        foreach ($this->conditions as $condition)
            $condition->modifyQuery($query);

        $items = $query->all();

        return (count($items) > 0) ? new Limited($items) : new Limitless();
    }

    /**
     * Check allowance by received allowance class
     *
     * Checks that received values can give access or not.
     *
     * @param AllowanceInterface $allowance
     * @return bool
     */
    public function checkAllowance(AllowanceInterface $allowance): bool
    {
        return $allowance->isAllow($this->userActivityRegistry);
    }
}