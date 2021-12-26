<?php

namespace App\Jobs\QueueExtension;

trait ReleaseHelperTrait {

	public function releaseCleanAttempt($delay = 1) {
		$copy = clone $this;
		unset($copy->job);
		dispatch($copy->delay($delay));
		return 0;
	}

	public function releaseAgain($delay = 1) {
		if ($this->job) {
			$this->release($delay);
		} else {
			dispatch($this->delay($delay));
		}
		return 0;
	}

	public function releaseIfWasLessAttempts($maxAttempts, $delayAttemptFactor = 0, $delaySeconds = 0) {
		$delay = $delaySeconds > 0 ? $delaySeconds : (int)($this->attempts() * $delayAttemptFactor);
		if ($this->attempts() <= $maxAttempts) {
			$this->releaseAgain($delay);
		}
		return 0;
	}

}
