<?php

namespace sofe\pmutil;

use pocketmine\plugin\PluginBase as PmBase;
use pocketmine\schedule\PluginTask;

class PluginBase extends PmBase{
	/**
	 * Pass in a generator that yields int. Each yield will behave as if sleep() is called, but only the execution of this function will pause.
	 *
	 * Code before the first yield will be executed immediately.
	 *
	 * Each yield expression returns the time elapsed since sleepy() has been called, in units specified by $units (i.e. ticks / $unit)
	 *
	 * @param \Generator|callable $generator a Generator object, or a callable that returns a Generator, yielding int
	 * @param int $unit This number multiplies the yielded values in $generator to be the number of ticks to delay.
	 */
	public function sleepy($generator, int $unit = 1){
		if(\is_callable($generator)) $generator = $generator();
		if(!($generator instanceof \Generator)) throw new \TypeError("\$generator must be a Generator object or a callable returning a Generator object");
		$task = new class($this, $generator) extends PluginTask{
			private $generator;
			private $unit;
			private $initial;
			public function __construct(PluginBase $plugin, \Generator $generator, $unit){
				parent::__construct($plugin);
				$this->generator = $generator;
				$this->unit = $unit;
				$this->initial = $plugin->getServer()->getTick();
			}
			public function onRun(int $ticks){
				$delay = $this->generator->send($ticks === \PHP_INT_MAX ? \PHP_INT_MAX : (($ticks - $this->initial) / $this->unit));
				if(!\is_int($delay)) throw new \TypeError("Generators passed to sleepy() must yield int");
				$this->owner->getServer()->getScheduler()->scheduleDelayedTask($this, $delay * $this->unit);
			}
		};
		$this->getServer()->getScheduler()->scheduleDelayedTask($task, $generator->current() * $unit);
	}

	public function getResourceContents(string $path) : ?string{
		$fh = $this->getResource($path);
		if($fh === null) return null;
		$ret = \stream_get_contents($fh);
		\fclose($fh);
		return $ret;
	}
}
