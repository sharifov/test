<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 20/11/2019
 * Time: 09:50 AM
 */

namespace modules\abac\components;

use common\models\Employee;
use modules\abac\src\entities\AbacPolicy;
use Yii;
use yii\base\Component;
use yii\caching\TagDependency;
use yii\httpclient\Request;
use Casbin\Enforcer;

/**
 * Class AbacComponent
 * @package modules\abac\components
 *
 * @property array $modules
 * @property bool $cacheEnabled
 * @property string $cacheKey
 * @property string $cacheTagDependency
 * @property string $abacModelPath
 * @property Enforcer $enforser
 * @property Request $request
 */

class AbacComponent extends Component
{
    public array $modules = [];

    public bool $cacheEnabled = true;
    public string $cacheKey = 'abac-policy';
    public string $cacheTagDependency = 'abac-tag-dependency';

    public string $abacModelPath = '@common/config/casbin/abac_model.conf';

    private $enforser;

    private ?array $objectList = null;
    private ?array $objectActionList = null;
    private ?array $objectAttributeList = null;
    private ?array $defaultAttributeList = null;

    public function init(): void
    {
        parent::init();

        $this->enforser = new Enforcer(
            Yii::getAlias($this->abacModelPath),
            Yii::getAlias("@common/config/casbin/policy_list.csv")
        );
        //$this->initRequest();
    }

    /**
     * @return \stdClass
     */
    private function getEnv()
    {
        /** @var Employee $me */
        $me = Yii::$app->user->identity;

        $user = new \stdClass();
        $user->id = $me->id;
        $user->username = $me->username;
        $user->roles =  $me->getRoles(true);
        $user->projects = $me->access->getAllProjects('key'); //getProjects();
        $user->groups = $me->access->getAllGroups();

        $request = new \stdClass();
        $request->controller = Yii::$app->controller->uniqueId;
        $request->action = Yii::$app->controller->action->uniqueId;
        $request->url = Yii::$app->request->url;
        $request->ip = Yii::$app->request->getUserIP();
        $request->get = Yii::$app->request->get();

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

        return $obj;
    }

    public function can(\stdClass $subject, string $object, string $action): bool
    {

        // VarDumper::dump($obj, 10, true);        exit;

       // $sub = new \stdClass();
        $subject->env = $this->getEnv();
        //$sub->data = $subject;

        if ($this->enforser->enforce($subject, $object, $action) === true) {
            return true;
        }
        return false;
    }



    public function getObjectList(): array
    {
        if ($this->objectList === null) {
            $objectList = [];
            if ($this->modules) {
                /** @var \modules\abac\src\entities\AbacInterface $module */
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

    public function getObjectActionList(): array
    {
        if ($this->objectActionList === null) {
            $list = [];
            if ($this->modules) {
                /** @var \modules\abac\src\entities\AbacInterface $module */
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

    public function getObjectAttributeList(): array
    {
        if ($this->objectAttributeList === null) {
            $list = [];
            if ($this->modules) {
                /** @var \modules\abac\src\entities\AbacInterface $module */
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
    public function getDefaultAttributeList(): array
    {
        if ($this->defaultAttributeList === null) {
            $this->defaultAttributeList = AbacBaseModel::getDefaultAttributeList();
        }
        return $this->defaultAttributeList;
    }



    /**
     * @return array
     */
    public function getOperators(): array
    {
        $operators = [
        'equal',
        'not_equal',
        'in',
        'not_in',
        'less',
        'less_or_equal',
        'greater',
        'greater_or_equal',
        'between',
        'not_between',
        'begins_with',
        'not_begins_with',
        'contains',
        'not_contains',
        'ends_with',
        'not_ends_with',
        'is_empty',
        'is_not_empty',
        'is_null',
        'is_not_null'];

        $operators[] = ['type' => '==', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '!=', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '<=', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '>=', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '<', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => '>', 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];

        $operators[] = ['type' => AbacBaseModel::OP_MATCH, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => AbacBaseModel::OP_IN_ARRAY, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];
        $operators[] = ['type' => AbacBaseModel::OP_NOT_IN_ARRAY, 'optgroup' => 'custom', 'nb_inputs' => 1, 'multiple' => false, 'apply_to' => "['number', 'string']"];

        return $operators;
    }

    /**
     * @param string|null $object
     * @return array
     */
    public function getActionListByObject(?string $object = null): array
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
    public function getAttributeListByObject(?string $object = null): array
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
    public function getPolicyListContentWOCache(): string
    {
        $rows = [];
        $policyModel = AbacPolicy::find()->select([
            'ap_rule_type',
            'ap_subject',
            'ap_object',
            'ap_action',
            'ap_effect'
        ])->orderBy(['ap_sort_order' => SORT_ASC, 'ap_id' => SORT_ASC])->all();
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
        return implode("\r\n", $rows);
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

                Yii::$app->cache->set(
                    $this->getCacheKey(),
                    $policyListContent,
                    0,
                    new TagDependency(['tags' => $this->getCacheTagDependency()])
                );
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
        return $this->cacheEnabled;
    }
}
