<?php

namespace src\yii\data;

use yii\data\ActiveDataProvider;
use yii\base\InvalidConfigException;
use yii\db\ActiveQueryInterface;
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
            $query->limit((int)($pagination->getLimit() + 1));
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

    public function prepareKeys($models)
    {
        $keys = [];
        if ($this->key !== null) {
            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } elseif ($this->query instanceof ActiveQueryInterface) {
            /* @var $class \yii\db\ActiveRecordInterface */
            $class = $this->query->modelClass;
            $pks = $class::primaryKey();
            if (count($pks) === 1) {
                $pk = $pks[0];
                foreach ($models as $model) {
                    $keys[] = $model[$pk];
                }
            } else {
                foreach ($models as $model) {
                    $kk = [];
                    foreach ($pks as $pk) {
                        $kk[$pk] = $model[$pk];
                    }
                    $keys[] = $kk;
                }
            }

            return $keys;
        }

        return array_keys($models);
    }
}
