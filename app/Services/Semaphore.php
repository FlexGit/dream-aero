<?php

namespace App\Services;

use Redis;

class Semaphore {

	/**
	 * Получить блокировку или die в случае невозможности
	 * @param string $semName
	 * @param int $slotCount
	 * @param int $maxWaitSeconds
	 * @param int $ttlSeconds
	 * @return Semaphore
	 */
	public static function getSemaphore($semName, $slotCount, $maxWaitSeconds, $ttlSeconds) {
		try {
			return new static($semName, $slotCount, $maxWaitSeconds, $ttlSeconds);
		} catch (\Exception $e) {
			\Log::error($e->getMessage(), [$e->getTraceAsString()]);
			die($e->getMessage());
		}
	}

	/**
	 * Получить блокировку или null в случае невозможности
	 * @param string $semName
	 * @param int $slotCount
	 * @param int $maxWaitSeconds
	 * @param int $ttlSeconds
	 * @return Semaphore|null
	 */
	public static function getSemaphoreOrNull($semName, $slotCount, $maxWaitSeconds, $ttlSeconds) {
		try {
			return new static($semName, $slotCount, $maxWaitSeconds, $ttlSeconds);
		} catch (\Exception $e) {
			return null;
		}
	}

	protected $name = null;
	protected $slotIndex = null;

	public function __construct($semName, $slotCount, $maxWaitSeconds, $ttlSeconds) {
		$slotCount = (int)$slotCount;
		if ($slotCount <= 0) $slotCount = 1;
		$ttlSeconds = (int)$ttlSeconds;
		if ($ttlSeconds <= 0) $ttlSeconds = 1;

		$key = static::getKey($semName);
		// проверяем все слоты на наличие ttl
		// если для какого-то слота не задан ttl, задаём его
		for ($slot = 0; $slot < $slotCount; ++$slot) {
			if (Redis::ttl($key.$slot) == -1) {
				Redis::expire($key.$slot, $ttlSeconds);
			}
		}

		// пытаемся получить эксклюзивный доступ по ключу за отведённое время ожидания, или throw exception
		$foundSlot = -1;
		$w = 0;
		while (true) {
			// ищем свободный слот, чтобы занять его для себя
			for ($slot = 0; $slot < $slotCount; ++$slot) {
				if (Redis::setnx($key.$slot, 1)) {
					$foundSlot = $slot;
					break 2; // выходим из while
				}
			}
			// спим до следующей итерации поиска слота
			usleep(50000);
			$w += 0.05;
			if ($w >= $maxWaitSeconds) throw new \Exception('Cannot find an empty slot in semaphore '.$key);
		}
		
		// теперь выставляем время жизни и запоминаем имя лока для удаления
		Redis::expire($key.$foundSlot, $ttlSeconds);
		$this->name = $semName;
		$this->slotIndex = $foundSlot;
	}

	public function close() {
		if ($this->name) {
			Redis::del(static::getKey($this->name).$this->slotIndex);
			$this->name = null;
			$this->slotIndex = null;
		}
	}

	public function __destruct() {
		$this->close();
	}

	protected static function getKey($semName) {
		return 'sem_' . $semName;
	}

}
