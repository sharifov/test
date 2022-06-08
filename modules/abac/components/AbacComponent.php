<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\abac\components;

use Casbin\CoreEnforcer;
use common\models\Employee;
use modules\abac\src\entities\AbacPolicy;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use stdClass;
use Yii;
use yii\base\Component;
use yii\caching\TagDependency;
use yii\helpers\VarDumper;
use yii\httpclient\Request;
use Casbin\Enforcer;
use modules\abac\src\entities\AbacInterface;

/**
 * Class AbacComponent
 *
 * @property AbacInterface[] $modules
 * @property string $cacheKey
 * @property string $cacheTagDependency
 * @property string $abacModelPath
 * @property Enforcer $enforser
 * @property-read stdClass $env
 * @property-read string $policyListContent
 * @property-read string $policyListContentWOCache
 * @property Request $request
 * @property array $scanDirs
 * @property array scanExtMask
 * @property bool $cacheEnable
 */
class AbacComponent extends Component
{
    public array $modules = [];
    public bool $cacheEnable = true;
    public bool $cacheEnforceEnable = true;
    public int $cacheEnforceDuration = 3600 * 24;
    public string $cacheKey = 'abac-policy';
    public string $cacheTagDependency = 'abac-tag-dependency';
    public string $abacModelPath = '@common/config/casbin/abac_model.conf';
    public array $scanDirs = [
        '/modules/',
        '/frontend/',
        '/common/',
        '/sales/',
        '/src/',
    ];
    public array $scanExtMask = ['*.php'];

    private Enforcer $enforser;

    private ?array $objectList = null;
    private ?array $objectActionList = null;
    private ?array $objectAttributeList = null;
    private ?array $defaultAttributeList = null;

    public function init(): void
    {
        parent::init();
        $policyListContent = $this->getPolicyListContent();
        // $policyListContent = file_get_contents(Yii::getAlias("@common/config/casbin/policy_list.csv"));

        // VarDumper::dump($policyListContent);

        if (!$policyListContent) {
            $policyListContent = 'p, true, *, (access), deny';
        }

        $adp = CasbinCacheAdapter::newAdapter($policyListContent);

        $this->enforser = new Enforcer(
            Yii::getAlias($this->abacModelPath),
            $adp
            //Yii::getAlias("@common/config/casbin/policy_list.csv")
        );
        $this->enforser->addFunction('ownRegEx', static function ($arg): string {
            return str_replace(['(', ')'], ['(^', '$)'], $arg);
        });
    }

    /**
     * @param Employee $me
     * @return \stdClass
     */
    private function getEnv(Employee $me)
    {
        /** @var Employee $me */
        $user = new \stdClass();
        $user->id = $me->id;
        $user->username = $me->username;

        $user->roles =  $me->getRoles(true);

        $user->projects = $me->access->getAllProjects('key'); //getProjects();
        $user->groups = $me->access->getAllGroups();
        $user->departments = $me->access->getAllDepartments();

        $request = new \stdClass();
        $request->controller = Yii::$app->controller->uniqueId;
        $request->action = Yii::$app->controller->action->uniqueId;

        if (Yii::$app->request instanceof \yii\base\Request && !Yii::$app->request->isConsoleRequest) {
            $request->url = Yii::$app->request->url ?? null;
            $request->ip = Yii::$app->request->getUserIP() ?? null;
        }
        // $request->get = Yii::$app->request->get();

        $dt = new \stdClass();
        $dt->date = date('Y-m-d');
        $dt->time = date('H:i');
        $dt->year = (int) date('Y');
        $dt->month = (int) date('n');
        $dt->month_name = date('M');
        $dt->dow = (int) date('N');
        $dt->dow_name = date('D');
        $dt->day = (int) date('j');
        $dt->hour = (int) date('G');
        $dt->min = (int) date('i');

        $obj = new \stdClass();
        $obj->req = $request;
        $obj->user = $user;
        $obj->dt = $dt;
        $obj->available = true;

        return $obj;
    }

    /**
     * @param stdClass|null $subject
     * @param string $object
     * @param string $action
     * @param Employee|null $user
     * @return bool|null
     */
    final public function can(?\stdClass $subject, string $object, string $action, ?Employee $user = null): ?bool
    {
        if (!$subject) {
            $subject = new \stdClass();
        }

        try {
            if (!$user) {
                $user = Auth::user();
            }

            $subject->env = $this->getEnv($user);
            $enforserObj = $this->enforser;


            if ($this->cacheEnforceEnable) {
                $preparedSubject = self::prepareSubject($subject);
                $keyCache = self::cacheCanGenerate($preparedSubject, $object, $action);

                $data = Yii::$app->cache->get($keyCache);
                if ($data === false) {
                    $data = (string) self::canEnforce($enforserObj, $subject, $object, $action);
                    $dependency = new TagDependency([
                        'tags' => [AbacPolicy::CACHE_KEY . $object, AbacPolicy::CACHE_KEY],
                    ]);
                    Yii::$app->cache->set($keyCache, $data, $this->cacheEnforceDuration, $dependency);
                    unset($dependency);
                }
                return (bool) $data;
            }

            return self::canEnforce($enforserObj, $subject, $object, $action);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable, true), 'AbacComponent::can');
            return null;
        }
    }

    private static function prepareSubject(\stdClass $origSubject): \stdClass
    {
        $subject = clone $origSubject;
        if ($subject->env->dt->time ?? null) {
            unset($subject->env->dt->time);
        }
        if ($subject->env->dt->hour ?? null) {
            unset($subject->env->dt->hour);
        }
        if ($subject->env->dt->min ?? null) {
            unset($subject->env->dt->min);
        }
        return $subject;
    }

    /**
     * @throws \Casbin\Exceptions\CasbinException
     */
    private static function canEnforce(Enforcer $enforserObj, \stdClass $subject, string $object, string $action): bool
    {
        return $enforserObj->enforce($subject, $object, $action) === true;
    }

    private static function cacheCanGenerate(\stdClass $subject, string $object, string $action, string $algo = 'md4'): string
    {
        return hash(
            $algo,
            \src\helpers\text\HashHelper::generateHashFromObject($subject) . $object . $action
        );
    }

    /**
     * @return array
     */
    final public function getObjectList(): array
    {
        if ($this->objectList === null) {
            $objectList = [];
            if ($this->modules) {
                foreach ($this->modules as $module) {
                    $objects = $module::getObjectList();
                    if ($objects) {
                        $objectList = array_merge($objectList, $objects);
                    }
                }
            }
            $this->objectList = $objectList;
        }
        return $this->objectList;
    }

    /**
     * @return array
     */
    final public function getObjectActionList(): array
    {
        if ($this->objectActionList === null) {
            $list = [];
            if ($this->modules) {
                foreach ($this->modules as $module) {
                    $objects = $module::getObjectActionList();
                    if ($objects) {
                        $list = array_merge($list, $objects);
                    }
                }
            }
            $this->objectActionList = $list;
        }
        return $this->objectActionList;
    }

    /**
     * @return array
     */
    final public function getObjectAttributeList(): array
    {
        if ($this->objectAttributeList === null) {
            $list = [];
            if ($this->modules) {
                foreach ($this->modules as $module) {
                    $objects = $module::getObjectAttributeList();
                    if ($objects) {
                        $list = array_merge($list, $objects);
                    }
                }
            }
            $this->objectAttributeList = $list;
        }
        return $this->objectAttributeList;
    }

    /**
     * @return array
     */
    final public function getDefaultAttributeList(): array
    {
        if ($this->defaultAttributeList === null) {
            $this->defaultAttributeList = AbacBaseModel::getDefaultAttributeList();
        }
        return $this->defaultAttributeList;
    }


    /**
     * @param string|null $object
     * @return array
     */
    final public function getActionListByObject(?string $object = null): array
    {
        $data = [];
        if ($object) {
            $list = $this->getObjectActionList();
            if (isset($list[$object]) && is_array($list[$object])) {
                foreach ($list[$object] as $actionName) {
                    $data[$actionName] = $actionName;
                }
            }
        }
        return $data;
    }

    /**
     * @param string|null $object
     * @return array
     */
    final public function getAttributeListByObject(?string $object = null): array
    {
        $defaultList = $this->getDefaultAttributeList();
        $objList = [];
        if ($object) {
            $list = $this->getObjectAttributeList();
            if (isset($list[$object]) && is_array($list[$object])) {
                $objList = $list[$object];
            }
        }

        return array_merge($objList, $defaultList);
    }


    /**
     * @return string
     */
    final public function getPolicyListContentWOCache(): string
    {
        $rows = [];
        $policyModel = AbacPolicy::find()->select([
            'ap_rule_type',
            'ap_subject',
            'ap_object',
            'ap_action',
            'ap_effect'
        ])
            ->where(['ap_enabled' => true])
            ->orderBy(['ap_sort_order' => SORT_ASC, 'ap_id' => SORT_ASC])->all();
        if ($policyModel) {
            foreach ($policyModel as $policy) {
                if (empty($policy->ap_action) || empty($policy->ap_object) || empty($policy->ap_rule_type)) {
                    continue;
                }

                $row = [];
                $row[] = $policy->ap_rule_type;
                $row[] = empty($policy->ap_subject) ? 'true' : $policy->ap_subject;
                $row[] = $policy->ap_object;
                $row[] = $policy->ap_action;
                $row[] = $policy->getEffectName();
                $rows[] = implode(', ', $row);
            }
        }
        unset($policyModel);
        return implode(PHP_EOL, $rows);
    }

    /**
     * @param bool|null $enabled
     * @return int
     */
    final public function getPolicyListCount(?bool $enabled = null): int
    {
        $query = AbacPolicy::find();
        if ($enabled !== null) {
            $query->where(['ap_enabled' => $enabled]);
        }
        $count = $query->count();

        return $count ? (int) $count : 0;
    }


    /**
     * @return string
     */
    public function getPolicyListContent(): string
    {
        if ($this->getCacheEnabled()) {
            $policyListContent = Yii::$app->cache->get($this->getCacheKey());
            if ($policyListContent === false) {
                $policyListContent = $this->getPolicyListContentWOCache();

                if ($policyListContent) {
                    Yii::$app->cache->set(
                        $this->getCacheKey(),
                        $policyListContent,
                        0,
                        new TagDependency(['tags' => $this->getCacheTagDependency()])
                    );
                }
            }
        } else {
            $policyListContent = $this->getPolicyListContentWOCache();
        }
        return $policyListContent;
    }

    /**
     * @return string
     */
    final public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @return string
     */
    final public function getCacheTagDependency(): string
    {
        return $this->cacheTagDependency;
    }

    /**
     * @return bool
     */
    final public function getCacheEnabled(): bool
    {
        return $this->cacheEnable;
    }

    /**
     * @return bool
     */
    public function invalidatePolicyCache(): bool
    {
        $cacheTagDependency = $this->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
            return true;
        }
        return false;
    }
}
