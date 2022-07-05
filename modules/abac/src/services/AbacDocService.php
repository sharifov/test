<?php

namespace modules\abac\src\services;

use modules\abac\components\AbacComponent;
use modules\abac\src\entities\abacDoc\AbacDoc;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class AbacDocService
 * @property AbacComponent $abacComponent
 * @property string $rootDirectory
 */
class AbacDocService
{
    private AbacComponent $abacComponent;
    private string $rootDirectory;

    /**
     * @param AbacComponent|null $abacComponent
     * @param string|null $rootDir
     */
    public function __construct(?AbacComponent $abacComponent = null, ?string $rootDir = null)
    {
        $this->abacComponent = $abacComponent ?? \Yii::$container->get(AbacComponent::class);
        $this->rootDirectory = $rootDir ?? Yii::getAlias('@root/');
    }

    public function parseFiles(): array
    {
        $files = [];
        foreach ($this->abacComponent->scanDirs as $scanDir) {
            $files = ArrayHelper::merge(
                $files,
                FileHelper::findFiles(Yii::getAlias('@root' . $scanDir), ['only' => $this->abacComponent->scanExtMask])
            );
        }

        $data = [];
        foreach ($files as $index => $file) {
            if ($parseData = $this->parseFile($file)) {
                $data = ArrayHelper::merge($data, $parseData);
            }
        }
        return $data;
    }

    public function insertData(array $data): bool
    {
        try {
            if (!$transaction = \Yii::$app->db->beginTransaction()) {
                throw new \RuntimeException('Transaction not init');
            }
            \Yii::$app->db->createCommand()->truncateTable(AbacDoc::tableName())->execute();
            \Yii::$app->db->createCommand()->batchInsert(
                AbacDoc::tableName(),
                ['ad_file', 'ad_line', 'ad_subject', 'ad_object', 'ad_action', 'ad_description', 'ad_created_dt'],
                $data
            )->execute();

            $transaction->commit();
        } catch (\Throwable $throwable) {
            if (isset($transaction)) {
                $transaction->rollBack();
            }
            throw new \RuntimeException($throwable->getMessage());
        }
        return true;
    }

    /**
     * @throws \Exception
     */
    private function parseFile(string $filename): array
    {
        $data = [];
        $content = file_get_contents($filename);
        if ($content) {
            $tokens = token_get_all($content);
            foreach ($tokens as $token) {
                if (is_array($token)) {
                    if (strpos($token[1], '@abac') !== false) {
                        $doc = str_replace(['/', '*', '@abac'], '', $token[1]);
                        $docExploded = explode(',', $doc);

                        $data[] = [
                            'ad_file' => str_replace($this->rootDirectory, '', $filename),
                            'ad_line' => $token[2],
                            'ad_subject' => ($subject = ArrayHelper::getValue($docExploded, 0)) ? trim($subject) : null,
                            'ad_object' => ($object = ArrayHelper::getValue($docExploded, 1)) ? trim($object) : null,
                            'ad_action' => ($action = ArrayHelper::getValue($docExploded, 2)) ? trim($action) : null,
                            'ad_description' => ($description = ArrayHelper::getValue($docExploded, 3)) ? trim($description) : null,
                            'ad_created_dt' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }
        return $data;
    }
}
