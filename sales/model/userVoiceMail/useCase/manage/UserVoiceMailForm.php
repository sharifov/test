<?php

namespace sales\model\userVoiceMail\useCase\manage;

use sales\helpers\app\AppParamsHelper;
use sales\model\userVoiceMail\entity\UserVoiceMail;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class UserVoiceMailForm
 * @package sales\model\userVoiceMail\useCase\manage
 *
 * @property UploadedFile $recordFile
 * @property string $blobUrl
 */
class UserVoiceMailForm extends UserVoiceMail
{
    private const VOICE_RECORDS_DIR_NAME = 'voice-records';

    /**
     * @var UploadedFile
     */
    public $recordFile;

    /**
     * @var string
     */
    public $blobUrl;

    public function rules(): array
    {
        $parentRules = parent::rules();

        return ArrayHelper::merge($parentRules, [
            [['recordFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp3'],
            ['blobUrl', 'string']
        ]);
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        $this->saveFile();
        return parent::save($runValidation, $attributeNames); // TODO: Change the autogenerated stub
    }

    private function saveFile(): void
    {
        if ($this->recordFile) {
            $filePath = \Yii::getAlias(AppParamsHelper::getVoiceMailAlias());
            $fileName = self::VOICE_RECORDS_DIR_NAME . '/' . md5(uniqid()) . '.' . $this->recordFile->extension;
            if (!file_exists($filePath . self::VOICE_RECORDS_DIR_NAME)) {
                if (!mkdir($dir = $filePath . self::VOICE_RECORDS_DIR_NAME) && !is_dir($dir)) {
                    throw new \RuntimeException('Directory "' . $dir . '" was not created');
                }
            }
            $this->recordFile->saveAs($filePath . $fileName);
            $this->uvm_voice_file_message = '/' . $fileName;
        }
    }

    public function updateRow(): bool
    {
        $oldRecord = $this->oldAttributes['uvm_voice_file_message'];
        $this->save(false);
        if (($oldRecord && $this->uvm_voice_file_message && $oldRecord !== $this->uvm_voice_file_message) || ($oldRecord && !$this->uvm_voice_file_message)) {
            $this->deleteRecord($oldRecord);
        }
        return true;
    }
}
