<?php

namespace sales\behaviors\userModelSetting;

use sales\helpers\app\AppHelper;
use sales\model\userModelSetting\entity\UserModelSetting;
use sales\model\userModelSetting\repository\UserModelSettingRepository;
use sales\model\userModelSetting\service\UserModelSettingService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class FieldsUpdateBehavior
 *
 * @property string $targetClassName
 */
class UserModelSettingSearchBehavior extends Behavior
{
    public $targetClassName;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'fieldsSave',
        ];
    }

    public function fieldsSave(): void
    {
        try {
            if (!property_exists($this->owner, 'fields')) {
                throw new \RuntimeException('Property "fields" not found in "' .
                    $this->targetClassName . '". Please connect "UserModelSettingTrait"');
            }
            if (!isset($this->owner->currentUser->id)) {
                throw new \RuntimeException('Property "currentUser" not found in "' .
                    $this->targetClassName . '" or not filled.');
            }
            $fieldsValue = $this->owner->fields;
            $userId = $this->owner->currentUser->id;
            $userModelSettingRepository = new UserModelSettingRepository();
            if ($userModelSetting = UserModelSettingService::findLastByUserAndClass($userId, $this->targetClassName, UserModelSetting::DEFAULT_NAME)) {
                if ($userModelSetting->getFields() !== $fieldsValue) {
                    $userModelSetting->changeFields($fieldsValue);
                    $userModelSettingRepository->save($userModelSetting);
                }
            } else {
                $userModelSetting = UserModelSetting::create(
                    $userId,
                    $this->targetClassName,
                    ['fields' => $fieldsValue],
                    UserModelSetting::DEFAULT_NAME
                );
                $userModelSettingRepository->save($userModelSetting);
            }
        } catch (\Throwable $throwable) {
            \Yii::warning(
                AppHelper::throwableLog($throwable),
                'UserModelSettingFieldsBehavior:fieldsSave:Throwable'
            );
        }
    }
}
