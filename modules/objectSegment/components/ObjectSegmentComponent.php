<?php

namespace modules\objectSegment\components;

use Casbin\Model\FunctionMap;
use Casbin\Model\Model;
use Casbin\Util\Util;
use modules\objectSegment\src\contracts\ObjectSegmentDtoInterface;
use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\contracts\ObjectSegmentObjectInterface;
use modules\objectSegment\src\entities\ObjectSegmentListQuery;
use modules\objectSegment\src\entities\ObjectSegmentTypeQuery;
use modules\objectSegment\src\service\ObjectSegmentListService;
use src\helpers\app\AppHelper;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use yii\caching\TagDependency;

/**
 *  Class ObjectSegmentComponent
 *
 *  * @property ObjectSegmentObjectInterface[] $modules
 */
class ObjectSegmentComponent
{
    private const CACHE_KEY = 'object_segment_type_%s_policies_list';

    public string $cacheTagDependency = 'object-segment-tag-dependency';

    private const R_TOKENS = ['r_sub', 'r_obj'];
    private const P_TOKENS = ['p_sub_rule', 'p_obj'];
    private const EXP_STRING = 'keyMatch(r_obj, p_obj) && eval(p_sub_rule)';

    private $concatOperand = '&&';


    public array $modules = [];

    private ?array $objectList = null;

    private ?array $objectTypesList = null;

    private ?array $objectAttributeList = null;

    private ?array $defaultAttributeList = null;
    /**
     * FunctionMap.
     *
     * @var FunctionMap
     */
    protected $fm;

    protected ObjectSegmentListService $objectSegmentListService;

    public function __construct(ObjectSegmentListService $objectSegmentListService)
    {
        $this->objectSegmentListService = $objectSegmentListService;
    }

    final public function getAttributeListByObject(?string $object = null): array
    {
        $defaultList = $this->getDefaultAttributeList();
        $objList     = [];
        if ($object) {
            $list = $this->getObjectAttributeList();
            if (isset($list[$object]) && is_array($list[$object])) {
                $objList = $list[$object];
            }
        }

        return array_merge($objList, $defaultList);
    }

    /**
     * @param ObjectSegmentDtoInterface $subject
     * @param string $objectType
     * @return false|null
     */
    final public function segment(ObjectSegmentDtoInterface $subject, string $objectType)
    {
        if (!$subject) {
            $subject = new \stdClass();
        }
        try {
            $this->enforcing($subject, $objectType);
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'ObjectSegmentComponent::segment');
            return null;
        }

        return false;
    }

    /**
     * @param ObjectSegmentDtoInterface $subject
     * @param string $objectType
     * @return void
     * @throws \Casbin\Exceptions\CasbinException
     * @throws \yii\base\InvalidConfigException
     */
    protected function enforcing(ObjectSegmentDtoInterface $subject, string $objectType)
    {
        $this->fm  = Model::loadFunctionMap();
        $functions = $this->fm->getFunctions();

        $rTokens = self::R_TOKENS;
        $pTokens = self::P_TOKENS;

        $rvals = [$subject, $objectType];
        $rParameters = array_combine($rTokens, $rvals);

        if (false == $rParameters) {
            throw new \DomainException('invalid request size');
        }

        $expressionLanguage = $this->getExpressionLanguage($functions);

        $expString = self::EXP_STRING;
        $policies = $this->getPolicies($objectType);

        $policyLen = \count($policies);
        $oslIds = [];
        if (0 != $policyLen) {
            foreach ($policies as $oslId => $pvals) {
                $parameters = array_combine($pTokens, $pvals);
                if (false == $parameters) {
                    throw new \DomainException('invalid policy size');
                }

                $ruleNames = Util::getEvalValue($expString);
                $expWithRule = $expString;
                $pTokens_flipped = array_flip($pTokens);
                foreach ($ruleNames as $ruleName) {
                    if (isset($pTokens_flipped[$ruleName])) {
                        $rule = Util::escapeAssertion($pvals[$pTokens_flipped[$ruleName]]);
                        $expWithRule = Util::replaceEval($expWithRule, $rule);
                    } else {
                        throw new \DomainException('please make sure rule exists in policy when using eval() in matcher');
                    }

                    $expression = $expressionLanguage->parse($expWithRule, array_merge($rTokens, $pTokens));
                }

                $parameters = array_merge($rParameters, $parameters);
                $result     = $expressionLanguage->evaluate($expression, $parameters);
                if ($result) {
                    $oslIds[] = $oslId;
                }
            }
        }
        $assigmentService = ObjectSegmentAssigmentFactory::getService($objectType);
        $assigmentService->assign($subject->getEntityId(), $oslIds);

        return;
    }


    /**
     * @param array $functions
     *
     * @return ExpressionLanguage
     */
    protected function getExpressionLanguage(array $functions): ExpressionLanguage
    {
        $expressionLanguage = new ExpressionLanguage();
        foreach ($functions as $key => $func) {
            $expressionLanguage->register($key, function (...$args) use ($key) {
                return sprintf($key . '(%1$s)', implode(',', $args));
            }, function ($arguments, ...$args) use ($func) {
                return $func(...$args);
            });
        }

        return $expressionLanguage;
    }

    /**
     * returns array of policies with enabled/disabled rules
     * @param string $segmentKey
     * @return array
     */
    protected function getPolicies(string $segmentKey): array
    {
        $cacheKey = sprintf(self::CACHE_KEY, $segmentKey);
        $policyListContent = \Yii::$app->cache->get($cacheKey);
        if ($policyListContent === false) {
            $policyListContent = $this->getPolicyListContentWOCache($segmentKey);

            if ($policyListContent) {
                \Yii::$app->cache->set(
                    $cacheKey,
                    json_encode($policyListContent, true),
                    0,
                    new TagDependency(['tags' => $this->getCacheTagDependency()])
                );
            }
        } else {
            $policyListContent = json_decode($policyListContent, true);
        }
        $result   = [];
        foreach ($policyListContent as $policy) {
            if (!isset($result[$policy['osrObjectSegmentList']['osl_key']])) {
                $result[$policy['osrObjectSegmentList']['osl_key']] = [
                    $policy['osr_rule_condition'],
                    $segmentKey
                ];
            } else {
                $result[$policy['osrObjectSegmentList']['osl_key']][0] =  $result[$policy['osrObjectSegmentList']['osl_key']][0] . $this->concatOperand . $policy['osr_rule_condition'];
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    final private function getPolicyListContentWOCache(string $segmentKey): array
    {
        return $this->objectSegmentListService->getTransformedRulesListByTypeKey($segmentKey);
    }

    /**
     * @return array
     */
    final public function getDefaultAttributeList(): array
    {
        if ($this->defaultAttributeList === null) {
            $this->defaultAttributeList = ObjectSegmentBaseModel::getDefaultAttributeList();
        }
        return $this->defaultAttributeList;
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
    final public function getObjectTypes(): array
    {
        if ($this->objectTypesList === null) {
            $this->objectTypesList = ObjectSegmentTypeQuery::getObjectTypesList();
        }
        return $this->objectTypesList;
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
    public function invalidatePolicyCache(): bool
    {
        $cacheTagDependency = $this->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(\Yii::$app->cache, $cacheTagDependency);
            return true;
        }
        return false;
    }

    /**
     * @return array|null
     */
    final public function getObjectList(): ?array
    {
        if ($this->objectList === null) {
            $this->objectList = ObjectSegmentListQuery::getObjectList();
        }
        return $this->objectList;
    }
}
