<?php

/*
 * This file is part of the olvlvl/class-combinator package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace olvlvl\ClassCombinator;

/**
 * Combine classes loaded by the runner and combine their definition into a single string.
 */
class ClassCombinator
{
	/**
	 * @var callable
	 */
	private $provider;

	/**
	 * @var WeightComputer
	 */
	private $weightComputer;

	/**
	 * @var FileCombinator
	 */
	private $fileCombinator;

	/**
	 * @param callable|null $provider
	 * @param WeightComputer|null $weightComputer
	 * @param FileCombinator|null $fileCombinator
	 */
	public function __construct(
		callable $provider,
		WeightComputer $weightComputer = null,
		FileCombinator $fileCombinator = null
	) {
		$this->provider = $provider;
		$this->weightComputer = $weightComputer ?: new WeightComputer();
		$this->fileCombinator = $fileCombinator ?: new FileCombinator();
	}

	/**
	 * @param callable $runner Execute a part of your application.
	 *
	 * @return string
	 */
	public function __invoke($root)
	{
		$classes = ($this->provider)();

		/* @var $reflections \ReflectionClass[] */

		$reflections = [];

		foreach ($classes as $class_name) {
			$reflection = new \ReflectionClass($class_name);
			$reflections[$class_name] = $reflection;
		}

		$weights = ($this->weightComputer)($reflections);

		$files = [];

		foreach (array_keys($weights) as $class_name) {
			$files[] = $reflections[$class_name]->getFileName();
		}

		return ($this->fileCombinator)($files, $root);
	}
}
