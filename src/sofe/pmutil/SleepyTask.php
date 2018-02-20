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

use pocketmine\scheduler\PluginTask;

class SleepyTask extends PluginTask{
	private $generator;
	private $unit;
	private $initial;

	public function __construct(PluginBase $plugin, \Generator $generator, $unit){
		parent::__construct($plugin);
		$this->generator = $generator;
		$this->unit = $unit;
		$this->initial = $plugin->getServer()->getTick();
	}

	public function onRun(int $ticks) : void{
		$delay = $this->generator->send($ticks === \PHP_INT_MAX ? \PHP_INT_MAX : (($ticks - $this->initial) / $this->unit));
		if(!$this->generator->valid()){
			return;
		}
		if(!\is_int($delay)){
			throw new \TypeError("Generators passed to sleepy() must yield int");
		}
		$this->owner->getServer()->getScheduler()->scheduleDelayedTask($this, $delay * $this->unit);
	}
}
