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
	private $discriminator;

	/**
	 * @var WeightComputer
	 */
	private $weightComputer;

	/**
	 * @var FileCombinator
	 */
	private $fileCombinator;

	/**
	 * @param callable|null $discriminator
	 * @param WeightComputer|null $weightComputer
	 * @param FileCombinator|null $fileCombinator
	 */
	public function __construct(
		callable $discriminator = null,
		WeightComputer $weightComputer = null,
		FileCombinator $fileCombinator = null
	) {
		$this->discriminator = $discriminator ?: new ClassDiscriminator();
		$this->weightComputer = $weightComputer ?: new WeightComputer();
		$this->fileCombinator = $fileCombinator ?: new FileCombinator();
	}

	/**
	 * @param string $root Project's root.
	 * @param callable $runner Execute a part of your application.
	 *
	 * @return string
	 */
	public function __invoke($root, callable $runner)
	{
		$classes = ($this->discriminator)($runner);

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
