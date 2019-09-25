<?php

namespace sales\forms;

use Yii;
use yii\bootstrap\Alert;

class NotificationsFormHelper {

	/**
	 * @var array
	 */
	private static $notifications = [];

	/**
	 * @param string $attribute
	 * @param string $message
	 */
	public static function addNotification(string $attribute, string $message): void
	{
		self::$notifications[$attribute] = $message;
	}

	/**
	 * @param string|null $attribute
	 * @return string|null
	 */
	public static function getNotification(string $attribute= null): ?string
	{
		return self::$notifications[$attribute] ?? null;
	}

	/**
	 * @return array
	 */
	public static function getAllListNotifications(): array
	{
		return self::$notifications;
	}

	/**
	 * @param string|null $attribute
	 * @return bool
	 */
	public static function hasNotifications(string $attribute = null): bool
	{
		if ($attribute) {
			return empty(self::$notifications[$attribute]) ? false : true;
		}

		return empty(self::$notifications) ? false : true;
	}

	/**
	 * @param string $alertClass
	 * @param string|null $attribute
	 * @return array|string
	 * @throws \Exception
	 */
	public static function getAlertsNotifications(string $alertClass, string $attribute = null)
	{
		if ($attribute && isset(self::$notifications[$attribute])) {
			return self::$notifications[$attribute] = Alert::widget([
				'options' => [
					'class' => $alertClass
				],
				'body' => self::$notifications[$attribute]
			]);
		}

		$notifications = [];
		foreach (self::$notifications as $key => $notification) {
			$notifications[$key] = Alert::widget([
				'options' => [
					'class' => $alertClass
				],
				'body' => $notification
			]);
		}

		return $notifications;
	}
}