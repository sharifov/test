<?php

namespace sales\services\clientChatCouchNote;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;

/**
 * Class ClientChatCouchNoteForm
 *
 * @property string|null $rid
 * @property string|null $message
 * @property string|null $alias
 */
class ClientChatCouchNoteForm extends Model
{
    public $rid;
    public $message;
    public $alias;

    private $clientChat;

    /**
     * ClientChatCouchNoteForm constructor
     * @param ClientChat|null $clientChat
     * @param Employee|null $employee
     * @param array $config
     */
    public function __construct(?ClientChat $clientChat = null, ?Employee $employee = null, $config = [])
    {
        parent::__construct($config);
        $this->rid = $clientChat ? $clientChat->cch_rid : null;
        $this->alias = $employee && $employee->userClientChatData ? $employee->userClientChatData->uccd_username : null;
    }

    public function rules(): array
    {
        return [
            [['rid', 'message', 'alias'], 'required'],

            [['message'], 'string', 'max' => 500],
            [['rid'], 'string', 'max' => 150],
            [['alias'], 'string', 'max' => 50],

            [['rid'], 'validateClientChat'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateClientChat($attribute): void
    {
        if (!$this->clientChat = ClientChat::find()->byRid($this->rid)->orderBy(['cch_id' => SORT_DESC])->one()) {
            $this->addError($attribute, 'ClientChat not found.');
        }
    }

    public function getClientChat(): ClientChat
    {
        return $this->clientChat;
    }
}
