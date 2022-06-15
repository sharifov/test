<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 2021-05-05
 * Time: 12:50 AM
 */

namespace modules\abac\components;

use Casbin\Persist\Adapter;
use Casbin\Persist\AdapterHelper;
use Casbin\Model\Model;
use Casbin\Persist\Adapters\Filter;
use Exception;

/**
 * Class CasbinCacheAdapter
 * @package modules\abac\components
 * @property  string $policyListContent
 */
class CasbinCacheAdapter implements Adapter
{
    use AdapterHelper;

    protected string $policyListContent;


    /**
     * Adapter constructor.
     *
     * @param string $policyListContent
     */
    public function __construct(string $policyListContent = '')
    {
        $this->policyListContent = $policyListContent;
    }

    /**
     * New a Adapter.
     *
     * @param string $policyListContent
     *
     * @return CasbinCacheAdapter
     */
    public static function newAdapter(string $policyListContent = ''): CasbinCacheAdapter
    {
        return new static($policyListContent);
    }

    /**
     * loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
    {
        $separator = PHP_EOL;
        $lines = explode($separator, $this->policyListContent);

        if ($lines) {
            foreach ($lines as $line) {
                $this->loadPolicyLine(trim($line), $model);
            }
        }
        unset($lines);
    }

    public function loadPolicyLine(string $line, Model $model, ?int $i = null): void
    {
        if ('' == $line) {
            return;
        }

        if ('#' == substr($line, 0, 1)) {
            return;
        }

        $tokens = array_map("trim", explode(", ", $line));

        $this->loadPolicyArray($tokens, $model);
    }

    /**
     * Loads only policy rules that match the filter.
     *
     * @param Model $model
     * @param string|CompositeExpression|Filter|Closure $filter
     * @throws Exception
     */
    public function loadFilteredPolicy(Model $model, $filter): void
    {
    }

    /**
     * saves all policy rules to the storage.
     *
     * @param Model $model
     * @throws Exception
     */
    public function savePolicy(Model $model): void
    {
    }

    /**
     * adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     * @throws Exception
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
    }


    /**
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     * @throws Exception
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
    }


    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param int $fieldIndex
     * @param string ...$fieldValues
     * @throws Exception
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param string[] $oldRule
     * @param string[] $newPolicy
     *
     * @throws Exception
     */
    public function updatePolicy(string $sec, string $ptype, array $oldRule, array $newPolicy): void
    {
    }
}
