<?php

namespace common\bootstrap;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use modules\fileStorage\src\FileSystem as FSystem;
use yii\base\BootstrapInterface;

class FileStorageBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $s3Params = \Yii::$app->params['s3'];
        $fsParams = \Yii::$app->params['fileStorage'];

        $container->setSingleton(FilesystemOperator::class, static function () use ($s3Params, $fsParams) {
            if ($fsParams['useRemoteStorage']) {
                $client = new S3Client($s3Params);
                $bucket = $fsParams['remoteStorage']['s3']['bucket'];
                $prefix = $fsParams['remoteStorage']['s3']['prefix'];
                $adapter = new AwsS3V3Adapter($client, $bucket, $prefix);
            } else {
                $adapter = new LocalFilesystemAdapter($fsParams['localStorage']['path']);
            }
            return new Filesystem($adapter);
        });

        $container->set(FSystem::class, static function ($container) {
            return new FSystem($container->get(FilesystemOperator::class));
        });
    }
}
