<?php

namespace src\rbac;

use yii\db\Query;

class RbacMoveToSrc
{
    private $files = [];

    public function move(string $path): void
    {
        $processing = 0;
        $notProcessing = 0;

        $rules = (new Query())->select('name')->from('auth_rule')->all();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($rules as $rule) {
                echo 'Rule ' . $rule['name'];
                $found = false;
                foreach ($this->getFiles($path) as $file) {
                    if (strpos(file_get_contents($file), $rule['name']) !== false) {
                        $class = $this->getNamespace($file);
                        echo ' => ' . $class . PHP_EOL;
                        $item = \Yii::createObject($class);
                        \Yii::$app->db->createCommand(
                            'UPDATE auth_rule SET `data` = :newdata WHERE name = :name',
                            [
                                ':newdata' => serialize($item),
                                ':name' => $item->name,
                            ]
                        )->execute();
                        $found = true;
                        $processing++;
                        break;
                    }
                }
                if (!$found) {
                    echo ' not found ' . PHP_EOL;
                    $notProcessing++;
                }
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            echo 'Error: ' . $e->getMessage() . PHP_EOL;
            $processing = 0;
            $notProcessing = 0;
        }

        echo 'Found ' . count($rules) . ' rules' . PHP_EOL;
        echo 'Processing ' . $processing . ' rules' . PHP_EOL;
        echo 'Not Processing ' . $notProcessing . ' rules' . PHP_EOL;
    }

    private function getNamespace(string $path)
    {
        $path = str_replace(['/', '.php'], ['\\', ''], $path);
        $rbacPosition = strpos($path, 'src\rbac');
        $path = substr($path, $rbacPosition);
        return $path;
    }

    private function getFiles(string $path): array
    {
        if ($this->files) {
            return $this->files;
        }
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $this->files = [];
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $this->files[] = $file->getPathname();
        }
        return $this->files;
    }
}
