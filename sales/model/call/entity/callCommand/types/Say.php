<?php

namespace sales\model\call\entity\callCommand\types;

use common\models\Language;
use sales\model\call\entity\callCommand\CallCommand;
use yii\base\Model;

/**
 * Class Say
 *
 * @property int $loop
 * @property string $text
 * @property string $voice
 * @property string $language
 * @property string $docUrl
 */
class Say extends Model implements CommandTypeInterface
{
    public $text;
    public $voice = 'man';
    public $loop = 1;
    public $language;

    public const VOICE_MAN = 'man';
    public const VOICE_WOMAN = 'woman';
    public const VOICE_ALICE = 'alice';

    public const VOICES_LIST = [
        self::VOICE_MAN => 'Man',
        self::VOICE_WOMAN => 'Woman',
        self::VOICE_ALICE => 'Alice',
    ];

    private string $docUrl = 'https://www.twilio.com/docs/voice/twiml/say';

    public $typeId = CallCommand::TYPE_SAY;
    public $sort;

    public function rules(): array
    {
        return [
            [['text', 'voice', 'loop', 'language'], 'required'],
            ['text', 'string', 'max' => 255],

            ['voice', 'string', 'max' => 255],
            ['voice', 'in', 'range' => array_keys(self::VOICES_LIST)],

            ['loop', 'integer', 'min' => 0],

            ['language', 'string', 'max' => 5],
            ['language', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true,
                'targetClass' => Language::class, 'targetAttribute' => ['language' => 'language_id']],

            [['typeId', 'sort'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getDocUrl(): string
    {
        return $this->docUrl;
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
