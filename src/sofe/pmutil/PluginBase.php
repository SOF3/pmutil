<?php

/*
 * pmutil: boilerplate utilities for PocketMine plugins
 * Copyright 2017-2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace sofe\pmutil;

class PluginBase extends \pocketmine\plugin\PluginBase{
	/**
	 * Pass in a generator that yields int. Each yield will behave as if sleep() is called, but only the execution of this function will pause.
	 *
	 * Code before the first yield will be executed immediately.
	 *
	 * Each yield expression returns the time elapsed since sleepy() has been called, in units specified by $units (i.e. ticks / $unit)
	 *
	 * @param \Generator|callable $generator a Generator object, or a callable that returns a Generator, yielding int
	 * @param int                 $unit      This number multiplies the yielded values in $generator to be the number of ticks to delay.
	 */
	public function sleepy($generator, int $unit = 1) : void{
		if(\is_callable($generator)){
			$generator = $generator();
		}
		if(!($generator instanceof \Generator)){
			throw new \TypeError("\$generator must be a Generator object or a callable returning a Generator object");
		}
		$this->getServer()->getScheduler()->scheduleDelayedTask(new SleepyTask($this, $generator, $unit), $generator->current() * $unit); // current() executes the initial code in the generator
	}

	public function getResourceContents(string $path) : ?string{
		$fh = $this->getResource($path);
		if($fh === null){
			return null;
		}
		$ret = \stream_get_contents($fh);
		\fclose($fh);
		return $ret;
	}

	private static $instance;

	public function onLoad(){
		self::$instance = $this;
	}

	public static function getInstance(){
		return self::$instance;
	}
}
