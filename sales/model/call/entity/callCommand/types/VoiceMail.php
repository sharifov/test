<?php

namespace sales\model\call\entity\callCommand\types;

use sales\model\call\entity\callCommand\CallCommand;
use sales\model\userVoiceMail\entity\UserVoiceMail;
use yii\base\Model;

/**
 * Class VoiceMail
 *
 * @property int $vm_id
 */
class VoiceMail extends Model implements CommandTypeInterface
{
    public $vm_id;

    public $typeId = CallCommand::TYPE_VOICE_MAIL;
    public $sort;

    public function rules(): array
    {
        return [
            [['vm_id'], 'required'],

            ['vm_id', 'integer'],
            ['vm_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => UserVoiceMail::class, 'targetAttribute' => ['vm_id' => 'uvm_id']],

            [['typeId', 'sort'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }
}
