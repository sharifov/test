<?php

namespace common\bootstrap;

use src\dispatchers\DeferredEventDispatcher;
use src\dispatchers\EventDispatcher;
use src\dispatchers\SimpleEventDispatcher;
use yii\base\BootstrapInterface;
use yii\di\Container;
use yii\helpers\ArrayHelper;

class SetUpListeners implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(EventDispatcher::class, DeferredEventDispatcher::class);

        $container->setSingleton(DeferredEventDispatcher::class, function (Container $container) {
            return new DeferredEventDispatcher(new SimpleEventDispatcher($container, $this->getListeners()));
        });
    }

    private function getListeners(): array
    {
        $listeners = [];
        $listeners = ArrayHelper::merge($listeners, $this->getListenersFromModules(__DIR__ . '/../../modules'));
        $listeners = ArrayHelper::merge($listeners, $this->getListenersFromFile(__DIR__ . '/../../src/listeners/listeners.php'));
        return $listeners;
    }

    private function getListenersFromModules(string $path): array
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException('invalid path to modules directory: ' . $path);
        }
        $listeners = [];
        if ($modules = array_diff(scandir($path), array('..', '.'))) {
            foreach ($modules as $module) {
                $dir = $path . '/' . $module;
                if ($dir !== '.' && $dir !== '..' && is_dir($dir)) {
                    $file = $dir . '/src/listeners/listeners.php';
                    if (file_exists($file)) {
                        $listeners = ArrayHelper::merge($listeners, $this->getListenersFromFile($file));
                    }
                }
            }
        }
        return $listeners;
    }

    private function getListenersFromFile(string $file): array
    {
        $listeners = require $file;
        if (!is_array($listeners)) {
            throw new \InvalidArgumentException('listeners must be array on file ' . $file);
        }
        return $listeners;
    }
}
