<?php

namespace modules\fileStorage\src\services\access;

use common\models\Lead;
use modules\fileStorage\FileStorageSettings;
use sales\auth\Auth;

/**
 * Class FileStorageAccessService
 */
class FileStorageAccessService
{
    public static function canLeadUploadWidget(Lead $lead): bool
    {
        return (
             FileStorageSettings::canUpload() &&
             Auth::can('lead-view/files/upload') &&
             Auth::can('lead/manage', ['lead' => $lead])
        );
    }

    public static function canEditTitleFile(): bool
    {
        return Auth::can('/file-storage/file-storage/title-update');
    }

    public static function canDeleteFile(): bool
    {
        return (
            Auth::can('/file-storage/file-storage/delete') ||
            Auth::can('/file-storage/file-storage/delete-ajax')
        );
    }
}
