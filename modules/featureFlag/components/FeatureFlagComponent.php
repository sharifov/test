<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 24/01/2022
 * Time: 09:50 AM
 */

namespace modules\featureFlag\components;

use Casbin\CoreEnforcer;
use common\models\Employee;
use modules\abac\src\entities\AbacPolicy;
use modules\featureFlag\src\entities\FeatureFlag;
use modules\featureFlag\src\FeatureFlagService;
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
 * Class FeatureFlagComponent
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
 *
 * @property array $ffList
 */
class FeatureFlagComponent extends Component
{
    public array $modules = [];
    public bool $cacheEnable = true;
    public string $cacheKey = 'feature-flag-policy';
    public string $cacheTagDependency = 'feature-flag-tag-dependency';
    public string $abacModelPath = '@common/config/casbin/abac_model.conf';
    public array $scanDirs = [
        '/modules/',
        '/frontend/',
        '/console/',
        '/common/',
        '/src/',
    ];
    public array $scanExtMask = ['*.php'];

    private Enforcer $enforser;

    private ?array $objectList = null;
    private ?array $objectActionList = null;
    private ?array $objectAttributeList = null;
    private ?array $defaultAttributeList = null;


    private ?array $ffList = null;

    public function init(): void
    {
        parent::init();

        //$this->cacheEnable = false;

//        $policyListContent = $this->getPolicyListContent();
//        // $policyListContent = file_get_contents(Yii::getAlias("@common/config/casbin/policy_list.csv"));
//
//        // VarDumper::dump($policyListContent);
//
//        if (!$policyListContent) {
//            $policyListContent = 'p, true, *, (access), deny';
//        }
//
//        $adp = CasbinCacheAdapter::newAdapter($policyListContent);
//
//        $this->enforser = new Enforcer(
//            Yii::getAlias($this->abacModelPath),
//            $adp
//            //Yii::getAlias("@common/config/casbin/policy_list.csv")
//        );
//        $this->enforser->addFunction('ownRegEx', static function ($arg): string {
//            return str_replace(['(', ')'], ['(^', '$)'], $arg);
//        });
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

//    /**
//     * @param stdClass|null $subject
//     * @param string $object
//     * @param string $action
//     * @param Employee|null $user
//     * @return bool|null
//     */
//    final public function can(?\stdClass $subject, string $object, string $action, ?Employee $user = null): ?bool
//    {
//        if (!$subject) {
//            $subject = new \stdClass();
//        }
//        try {
    ////            if (!$user && (Yii::$app instanceof \yii\web\Application) && Yii::$app->id === 'app-frontend') {
    ////                $user = Auth::user();
    ////            }
//            if (!$user) {
//                $user = Auth::user();
//            }
//
//            $subject->env = $this->getEnv($user);
//            //$sub->data = $subject;
//
//            if ($this->enforser->enforce($subject, $object, $action) === true) {
//                return true;
//            }
//        } catch (\Throwable $throwable) {
//            Yii::error(AppHelper::throwableLog($throwable, true), 'AbacComponent::can');
//            //VarDumper::dump(AppHelper::throwableLog($throwable), 10, true);
//            return null;
//        }
//
//        return false;
//    }


    public function getFFItem(string $key): ?array
    {
        if (empty($this->ffList[$key])) {
            return [];
        }
        return $this->ffList[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function can(string $key): bool
    {
        if ($this->ffList === null) {
            $this->ffList = $this->getFFList();
        }
        //VarDumper::dump($this->ffList ); exit;
        $response = false;
        $item = $this->getFFItem($key);

        //VarDumper::dump($item); exit;

        if ($item) {
            $type =  $item['ff_enable_type'] ? (int) $item['ff_enable_type'] : 0;
            if ($type === FeatureFlag::ET_ENABLED) {
                $response = true;
            } elseif ($type === FeatureFlag::ET_ENABLED_CONDITION) {
                $response = true;
            } elseif ($type === FeatureFlag::ET_DISABLED_CONDITION) {
                $response = true;
            }
        }
        return $response;
    }



    /**
     * @param string $key
     * @return mixed|null
     */
    final public function val(string $key)
    {
        if ($this->ffList === null) {
            $this->ffList = $this->getFFList();
        }

        $item = $this->getFFItem($key);
        if ($item) {
            return $item['ff_value'];
        }

        return null;
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
            $this->defaultAttributeList = FeatureFlagBaseModel::getDefaultAttributeList();
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
     * @return array
     */
    final public function getFFListWOCache(): array
    {
        $ffList = [];
        $list =  FeatureFlagService::getFeatureFlagList();


        if ($list) {
            foreach ($list as $key => $item) {
                if (!empty(FeatureFlag::TYPE_LIST[$item['ff_type']])) {
                    if ($item['ff_type'] === FeatureFlag::TYPE_ARRAY) {
                        $item['ff_value'] = @json_decode($item['ff_value'], true);
                    }
                    @settype($item['ff_value'], $item['ff_type']);
                }
                //$value = $item['ff_value'];
                $ffList[$item['ff_key']] = $item;
            }
        }



//        if ($list) {
//            foreach ($list as $item) {
//                $ffList[$item['ff_key']] = $item;
//            }
//        }
//
        return $ffList;
        /*

        $rows = [];
        $policyModel = FeatureFlag::find()->select([
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
                $row[] = $policy->ap_subject;
                $row[] = $policy->ap_object;
                $row[] = $policy->ap_action;
                $row[] = $policy->getEffectName();
                $rows[] = implode(', ', $row);
            }
        }
        unset($policyModel);
        return implode(PHP_EOL, $rows);*/
    }

//    /**
//     * @return int
//     */
//    final public function getFFListCount(): int
//    {
//        $count = FeatureFlag::find()->count();
//        return $count ? (int) $count : 0;
//    }


    /**
     * @return array
     */
    public function getFFList(): array
    {
        if ($this->getCacheEnabled()) {
            $ffList = Yii::$app->cache->get($this->getCacheKey());

            if ($ffList === false) {
                $ffList = $this->getFFListWOCache();

                if ($ffList) {
                    Yii::$app->cache->set(
                        $this->getCacheKey(),
                        $ffList,
                        0,
                        new TagDependency(['tags' => $this->getCacheTagDependency()])
                    );
                }
            }
        } else {
            $ffList = $this->getFFListWOCache();
        }
        return $ffList ?: [];
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
    public function invalidateCache(): bool
    {
        $cacheTagDependency = $this->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
            return true;
        }
        return false;
    }
}
