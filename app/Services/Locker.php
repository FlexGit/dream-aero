<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class Locker {

	/**
	 * Получить блокировку или die в случае невозможности
	 * @param string $lockName
	 * @param int $maxWaitSeconds
	 * @param int $ttlSeconds
	 * @return Locker
	 */
	public static function getLock($lockName, $maxWaitSeconds, $ttlSeconds) {
		try {
			return new Locker($lockName, $maxWaitSeconds, $ttlSeconds);
		} catch (\Exception $e) {
			\Log::error($e->getMessage(), [$e->getTraceAsString()]);
			die($e->getMessage());
		}
	}

	/**
	 * Получить блокировку или null в случае невозможности
	 * @param string $lockName
	 * @param int $maxWaitSeconds
	 * @param int $ttlSeconds
	 * @return Locker|null
	 */
	public static function getLockOrNull($lockName, $maxWaitSeconds, $ttlSeconds) {
		try {
			return new Locker($lockName, $maxWaitSeconds, $ttlSeconds);
		} catch (\Exception $e) {
			return null;
		}
	}

	protected $name = null;

	public function __construct($lockName, $maxWaitSeconds, $ttlSeconds) {
		$ttlSeconds = (int)$ttlSeconds;
		if ($ttlSeconds <= 0) $ttlSeconds = 1;
		$key = static::getKey($lockName);
		if (Redis::ttl($key) == -1) { // ключ есть, но для него не указан ttl, мы не можем это так оставить
			Redis::del($key); // удаляем битый ключ
		}
		// пытаемся получить эксклюзивный доступ по ключу за отведённое время ожидания, или throw exception
		$w = 0;
		while (Redis::setnx($key, 1) != true) {
			usleep(50000);
			$w += 0.05;
			if ($w >= $maxWaitSeconds) throw new \Exception('Cannot acquire lock '.$key);
		}
		// теперь выставляем время жизни и запоминаем имя лока для удаления
		if ($ttlSeconds > 0) Redis::expire($key, $ttlSeconds);
		$this->name = $lockName;
	}

	public function close() {
		if ($this->name) {
			Redis::del(static::getKey($this->name));
			$this->name = null;
		}
	}

	public function __destruct() {
		$this->close();
	}

	protected static function getKey($lockName) {
		return 'lock_' . $lockName;
	}

}
