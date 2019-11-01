<?php

namespace common\bootstrap;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\Lead2;
use common\models\LeadFlightSegment;
use common\models\LeadPreferences;
use common\models\Quote;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

class Logger implements BootstrapInterface
{
	private const CLASSES = [
		Client::class,
		ClientPhone::class,
		ClientEmail::class,
		Lead::class,
		Lead2::class,
		LeadPreferences::class,
		LeadFlightSegment::class,
		Quote::class,
	];

	/**
	 * @param \yii\base\Application $app
	 */
	public function bootstrap($app): void
	{
		$func =  static function (AfterSaveEvent $event) {
			foreach (self::CLASSES as $class) {
				if (get_class($event->sender) === $class) {

					$oldAttr = self::getOldAttrs($event);

					$newAttr = self::getNewAttrs($event);

					if (empty($oldAttr) && empty($newAttr)) {
						continue;
					}

					$log = \Yii::createObject(GlobalLogInterface::class);

					$log->log(
						new LogDTO(
							get_class($event->sender),
							$event->sender->attributes['id'],
							\Yii::$app->id,
							\Yii::$app->user->id ?? null,
							$oldAttr,
							$newAttr,
							null,
							GlobalLog::ACTION_TYPE_AR[$event->name] ?? null
						)
					);
				}
			}
		};

		Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_UPDATE, $func);
		Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_INSERT, $func);
	}

	/**
	 * @param AfterSaveEvent $event
	 * @return string
	 */
	private static function getNewAttrs(AfterSaveEvent $event): string
	{
		$newAttr = [];
		foreach ($event->changedAttributes as $key => $attribute) {
			if (array_key_exists($key, $event->sender->attributes)) {
				$newAttr[$key] = $event->sender->attributes[$key];
			}
		}
		return $newAttr ? json_encode($newAttr) : '';
	}

	/**
	 * @param AfterSaveEvent $event
	 * @return string|null
	 */
	private static function getOldAttrs(AfterSaveEvent $event): ?string
	{
		if ($event->name === ActiveRecord::EVENT_AFTER_INSERT) {
			$oldAttr = null;
		} else {
			$oldAttr = $event->changedAttributes ? json_encode($event->changedAttributes) : '';
		}
		return $oldAttr;
	}
}
