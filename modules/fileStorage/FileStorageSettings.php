<?php

namespace modules\fileStorage;

use yii\helpers\ArrayHelper;

class FileStorageSettings
{
    public static function isEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['file_storage_enabled'] ?? false);
    }

    public static function isUploadEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['file_upload_enabled'] ?? true);
    }

    public static function isDownloadEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['file_download_enabled'] ?? true);
    }

    public static function getUploadMaxSize()
    {
        return (int)(\Yii::$app->params['settings']['file_upload_max_size'] ?? 2) * 1024 * 1024;
    }

    public static function getMimeTypes(): array
    {
        return ArrayHelper::index(\Yii::$app->params['settings']['file_upload_allowed_mime_types'], fn ($value) => $value);
    }

    public static function uploadUserPeriodHours(): bool
    {
        return (int)(\Yii::$app->params['settings']['file_upload_user_period_hours'] ?? 24);
    }

    public static function uploadUserPeriodLimit(): bool
    {
        return (int)(\Yii::$app->params['settings']['file_upload_user_period_limit'] ?? 100);
    }

    public static function isEmailAttachEnabled(): bool
    {
        return (bool)(\Yii::$app->params['settings']['file_email_attach_enabled'] ?? true);
    }
}
