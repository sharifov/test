<?php

/**
 * @author AlexConnor
 * @cdata 2021-06-29
 */

namespace common\bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
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

    private Dotenv $dotenv;

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

    public function load(): self
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

            $this->dotenv = Dotenv::create($repository, $this->path, $this->file);
            $this->dotenv->load();

            //$dotenv->required(['YII_DEBUG', 'YII_ENV']);
        } catch (\Exception $e) {
            echo('Could not load Dotenv file. ERROR: ' . $e->getMessage());
            exit;
        }
        return $this;
    }

    public function validate(): self
    {
        try {
            $this->dotenv->required('YII_DEBUG')->notEmpty();
            $this->dotenv->required('YII_ENV')->notEmpty();

            $this->dotenv->required('common.config.main.components.db.dsn.dbname')->notEmpty();
            $this->dotenv->required('common.config.main.components.db.dsn.host')->notEmpty();

            $this->dotenv->required('common.config.main.components.cache.redis.hostname')->notEmpty();

            $this->dotenv->required('common.config.main.components.db_postgres.dsn.host')->notEmpty();
            $this->dotenv->required('common.config.main.components.db_postgres.username')->notEmpty();
        } catch (ValidationException $e) {
            echo('Dotenv validation failed. ERROR: ' . $e->getMessage());
            exit;
        }

        return $this;
    }
}
