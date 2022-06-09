<?php

namespace common\models\query;

use yii\data\ActiveDataProvider;
use yii\base\InvalidConfigException;
use yii\db\QueryInterface;

class BigActiveDataProvider extends ActiveDataProvider
{
    protected function prepareTotalCount()
    {
        return 0;
    }

    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;

        if (($pagination = $this->getPagination()) !== false) {
            $query->orderBy(['css_cs_id' => SORT_DESC])
                ->limit((int)($pagination->getLimit() + 1));
        }
        $res = $query->all($this->db);
        if (($pagination = $this->getPagination()) !== false) {
            $page = $pagination->getPage(true) + 1;
            $pagination->totalCount = ($page + 1) * $pagination->getPageSize();
            if (count($res) > $pagination->getPageSize()) {
                unset($res[count($res)]);
                $pagination->totalCount++;
            }
        }

        return $res;
    }
}
