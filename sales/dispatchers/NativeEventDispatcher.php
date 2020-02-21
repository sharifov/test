<?php
namespace sales\dispatchers;

use yii\base\Event;
use yii\helpers\VarDumper;

class NativeEventDispatcher
{
	public static $queue = [];

	public static function recordEvent(string $eventClass, string $eventName, array $handlerCallable, $params = null): self
	{
		if (is_callable($handlerCallable)) {
			self::registerEvent($eventClass, $eventName, $handlerCallable, $params);
		} else {
			foreach ($handlerCallable as $callable) {
				if (is_callable($callable)) {
					self::registerEvent($eventClass, $eventName, $callable, $params);
				} else {
					throw new \RuntimeException('Type of third parameter is not callable');
				}
			}
		}

		return new static;
	}

	public static function trigger(string $eventClass, string $eventName): void
	{
		if (isset(self::$queue[$eventClass])) {
			foreach (self::$queue[$eventClass] as $classEventName) {
				if ($eventName === $classEventName) {
					Event::trigger($eventClass, $classEventName);
					unset(self::$queue[$eventClass][$classEventName]);

					if (empty(self::$queue[$eventClass])) {
						unset(self::$queue[$eventClass]);
					}
				}
			}
		}
	}

	public static function triggerBy(string $eventClass): void
	{
		if (isset(self::$queue[$eventClass])) {
			foreach (self::$queue[$eventClass] as $classEventName) {
				Event::trigger($eventClass, $classEventName);
				unset(self::$queue[$eventClass][$classEventName]);
			}
			unset(self::$queue[$eventClass]);
		}
	}

	public static function triggerAll(): void
	{
		\Yii::warning(VarDumper::dumpAsString(self::$queue));
		foreach (self::$queue as $key => $eventClass) {
			if (is_array($eventClass)) {
				foreach ($eventClass as $eventName) {
					Event::trigger($key, $eventName);
				}
			} else {
				Event::trigger($key, self::$queue[$key]);
			}
		}
		self::$queue = [];
	}

	public static function offEvent(string $eventClass, string $eventName, array $handlerClosure = null): self
	{
		if (isset(self::$queue[$eventClass])) {
			foreach (self::$queue[$eventClass] as $classEventName) {
				if ($eventName === $classEventName) {
					Event::off($eventClass, $eventName);
					unset(self::$queue[$eventClass]);
				}
			}
		}

		return new static;
	}

	public static function offAllEvents(): self
	{
		Event::offAll();
		self::$queue = [];

		return new static;
	}

	private static function registerEvent(string $className, string $eventName, array $handlerCallable, $params = null): void
	{
		if (!(isset(self::$queue[$className]) && in_array($eventName, self::$queue[$className], true))) {
			self::$queue[$className][] = $eventName;
		}
		Event::on($className, $eventName, $handlerCallable, $params);
	}
}