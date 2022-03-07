<?php

namespace frontend\models\search;

use common\components\AppService;
use yii\base\Model;
use yii\data\ArrayDataProvider;

/**
 * @property string $name
 * @property string $type
 * @property string $version
 * @property string $license
 * @property string $source
 * @property string $authors
 * @property string $comments
 */
class ComposerLockSearch extends Model
{
    public $name;
    public $type;
    public $version;
    public $license;
    public $source;
    public $authors;
    public $comments;

    public function rules(): array
    {
        return [
            ['name', 'string'],
            ['type', 'string'],
            ['version', 'string'],
            ['license', 'string'],
            ['source', 'string'],
            ['authors', 'string'],
            ['comments', 'string'],
        ];
    }

    /**
     * @param $params
     * @return ArrayDataProvider
     */
    public function search($params): ArrayDataProvider
    {
        try {
            $data = AppService::getComposerLockData();
        } catch (\Throwable $throwable) {
            $data = [];
        }

        if ($data && $this->load($params) && $this->validate()) {
            $data = array_filter($data, function ($value) {
                $conditions = [true];
                if (!empty($this->name)) {
                    $conditions[] = strpos($value['name'], $this->name) !== false;
                }
                if (!empty($this->type)) {
                    $conditions[] = strpos($value['type'], $this->type) !== false;
                }
                if (!empty($this->version)) {
                    $conditions[] = strpos($value['version'], $this->version) !== false;
                }
                if (!empty($this->license)) {
                    $conditions[] = strpos($value['license'], $this->license) !== false;
                }
                if (!empty($this->source)) {
                    $conditions[] = strpos($value['source'], $this->source) !== false;
                }
                if (!empty($this->authors)) {
                    $conditions[] = strpos($value['authors'], $this->authors) !== false;
                }
                if (!empty($this->comments)) {
                    $conditions[] = strpos($value['comments'], $this->comments) !== false;
                }
                return array_product($conditions);
            });
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 200,
            ],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => ['name', 'type', 'version', 'license', 'source', 'authors', 'comments'],
            ],
        ]);

        return $dataProvider;
    }
}
