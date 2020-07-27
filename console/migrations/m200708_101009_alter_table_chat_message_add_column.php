<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\db\Migration;

/**
 * Class m200708_101009_alter_table_chat_message_add_column
 */
class m200708_101009_alter_table_chat_message_add_column extends Migration
{
	public function init()
	{
		$this->db = 'db_postgres';
		parent::init();
	}

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

		$db = ClientChatMessage::getDb();

    	$this->addColumn('{{%client_chat_message}}', 'ccm_cch_id', $this->integer()->after('ccm_id'));

    	$ridCollection = ClientChatMessage::find()->select(['ccm_rid'])->asArray()->all();

		if ($ridCollection) {
    		$ridCollection = \yii\helpers\ArrayHelper::getColumn($ridCollection, 'ccm_rid');

    		$clientChat = ClientChat::find()->select(['cch_id', 'cch_rid'])->where(['IN', 'cch_rid', $ridCollection])->asArray()->all();

    		foreach ($clientChat as $item) {
				$db->createCommand()->update('{{%client_chat_message}}', ['ccm_cch_id' => $item['cch_id']], ['ccm_rid' => $item['cch_rid']])->execute();
			}
		}

//		$this->dropColumn('{{%client_chat_message}}', 'ccm_rid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//		$this->addColumn('{{%client_chat_message}}', 'ccm_rid', $this->string(150)->after('ccm_id'));

		$clientChatCollection = ClientChat::find()->select(['cch_id', 'cch_rid'])->asArray()->all();

		$db = ClientChatMessage::getDb();

		foreach ($clientChatCollection as $item) {
			$db->createCommand()->update('{{%client_chat_message}}', ['ccm_rid' => $item['cch_rid']], ['ccm_cch_id' => $item['cch_id']])->execute();
		}
		$this->dropColumn('{{%client_chat_message}}', 'ccm_cch_id');

//		$this->alterColumn('{{%client_chat_message}}', 'ccm_rid', $this->string(150)->notNull());
	}
}
