<?php

/**
 * @author AlexConnor
 * @cdata 2021-06-29
 */

namespace common\bootstrap;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;
use Dotenv\Repository\RepositoryBuilder;

/**
 * Class EnvLoader
 *
 * @property string|null $path
 * @property string|null $file
 */
class EnvLoader
{

    /**
     * @var string Environment variable file directory
     */
    public string $path = '/';
    /**
     * @var string Use if custom environment variable file
     */
    public string $file = '.env';

    /**
     * @param string|null $path
     * @param string|null $file
     */
    public function __construct(?string $path = null, ?string $file = null)
    {
        if ($path !== null) {
            $this->path = $path;
        }
        if ($file !== null) {
            $this->file = $file;
        }
    }

    public function load()
    {
        try {
            $repository = RepositoryBuilder::createWithNoAdapters()
                ->addAdapter(EnvConstAdapter::class)
                //->addAdapter(ServerConstAdapter::class)
                ->addWriter(PutenvAdapter::class)
                ->immutable()
                ->make();

//            $repository = RepositoryBuilder::createWithDefaultAdapters()
//                ->addAdapter(PutenvAdapter::class)
//                ->immutable()
//                ->make();

            $dotenv = Dotenv::create($repository, $this->path, $this->file);
            $dotenv->load();

            //$dotenv->required(['YII_DEBUG', 'YII_ENV']);
        } catch (\Exception $e) {
            echo('Could not load Dotenv file. ERROR: ' . $e->getMessage());
            exit;
        }
    }
}
