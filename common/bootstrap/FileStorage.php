<?php

namespace common\bootstrap;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use modules\fileStorage\src\AwsS3Configurator;
use modules\fileStorage\src\AwsS3UrlGenerator;
use modules\fileStorage\src\Configurator;
use modules\fileStorage\src\FileSystem as FSystem;
use modules\fileStorage\src\LocalConfigurator;
use modules\fileStorage\src\LocalUrlGenerator;
use modules\fileStorage\src\UrlGenerator;
use yii\base\BootstrapInterface;

class FileStorage implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $s3Params = \Yii::$app->params['s3'];
        $fileStorageParams = \Yii::$app->params['fileStorage'];

        $container->set(FilesystemOperator::class, static function () use ($s3Params, $fileStorageParams) {
            if ($fileStorageParams['useRemoteStorage']) {
                $client = new S3Client($s3Params);
                $bucket = $fileStorageParams['remoteStorage']['s3']['bucket'];
                $prefix = $fileStorageParams['remoteStorage']['s3']['prefix'];
                $adapter = new AwsS3V3Adapter($client, $bucket, $prefix);
            } else {
                $adapter = new LocalFilesystemAdapter(
                    $fileStorageParams['localStorage']['path'],
                    PortableVisibilityConverter::fromArray(
                        $fileStorageParams['localStorage']['converterConfig']['fileDir'],
                        $fileStorageParams['localStorage']['converterConfig']['defaultForDirectories'],
                    ),
                );
            }
            return new Filesystem($adapter);
        });

        $container->set(Configurator::class, static function () use ($fileStorageParams) {
            if ($fileStorageParams['useRemoteStorage']) {
                $uploadConfig = $fileStorageParams['remoteStorage']['s3']['uploadConfig'] ?? [];
                return new AwsS3Configurator($uploadConfig);
            }
            $uploadConfig = $fileStorageParams['localStorage']['uploadConfig'] ?? [];
            return new LocalConfigurator($uploadConfig);
        });

        $container->set(FSystem::class, static function ($container) {
            return new FSystem($container->get(FilesystemOperator::class), $container->get(Configurator::class));
        });

        $container->set(UrlGenerator::class, static function () use ($fileStorageParams) {
            if ($fileStorageParams['useRemoteStorage']) {
                return new AwsS3UrlGenerator(
                    $fileStorageParams['remoteStorage']['cdn']['host'],
                    $fileStorageParams['remoteStorage']['cdn']['prefix']
                );
            }
            return new LocalUrlGenerator($fileStorageParams['localStorage']['url']);
        });
    }
}
