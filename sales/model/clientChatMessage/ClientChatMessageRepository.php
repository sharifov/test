<?php

namespace sales\model\clientChatMessage;

use sales\model\clientChatMessage\entity\ClientChatMessage;

class ClientChatMessageRepository
{
    /**
     * @param ClientChatMessage $message
     * @return ClientChatMessage
     */
	public static function saveMessage(ClientChatMessage $message): ClientChatMessage
    {
		return self::save($message, 0);
    }

    /**
     * @param ClientChatMessage $model
     * @param int $attempts
     * @return ClientChatMessage
     * @throws \Exception
     */
	public static function save(ClientChatMessage $model, int $attempts): ?ClientChatMessage
	{
        try {
            $model->save(false);
        } catch (\Throwable $e) {

            if(strpos($e->getMessage(), "no partition of relation")) {
                $dates = ClientChatMessage::partitionDatesFrom(date_create_from_format('Y-m-d H:i:s', $model->ccm_sent_dt));
                ClientChatMessage::createMonthlyPartition($dates[0], $dates[1]);

                //first attempt to create partition failed , something went wrong -> just ignore the message
                if ($attempts > 0) {
                    throw new \RuntimeException("unable to create client_chat_message partition");
                }

                self::save($model, ++$attempts);
            }
        }
		return $model;
	}
}