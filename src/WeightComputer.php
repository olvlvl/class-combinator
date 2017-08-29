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
 * Compute the weight of classes according to their relation to others.
 *
 * The more a class have relations, the more it sinks.
 */
class WeightComputer
{
	private $weights = [];
	private $reflections = [];

	/**
	 * @param \ReflectionClass[] $reflections
	 *
	 * @return array
	 */
	public function __invoke(array $reflections)
	{
		$this->reflections = $reflections;
		$this->weights = array_combine(array_keys($reflections), array_fill(0, count($reflections), null));

		foreach ($reflections as $class) {
			$this->computeClass($class);
		}

		arsort($this->weights);

		return $this->weights;
	}

	/**
	 * @param \ReflectionClass $class
	 *
	 * @return int
	 */
	private function computeClass(\ReflectionClass $class)
	{
		$weights = &$this->weights;
		$name = $class->name;
		$w = &$weights[$name];

		if ($w !== null) {
			return $w;
		}

		$w = 0;

		if ($class->isTrait()) {
			foreach ($this->reflections as $reflection) {
				foreach ($reflection->getTraits() as $trait) {
					if ($trait->name !== $name) {
						continue;
					}

					$w += 1 + $this->computeClass($reflection);
				}
			}
		} elseif ($class->isInterface()) {
			foreach ($this->reflections as $reflection) {
				foreach ($reflection->getInterfaces() as $interface) {
					if ($interface->name !== $name) {
						continue;
					}

					$w += 1 + $this->computeClass($reflection);
				}
			}
		} else {
			foreach ($this->reflections as $reflection) {
				$parent = $reflection->getParentClass();

				if (!$parent || $parent->name != $name) {
					continue;
				}

				$w += 1 + $this->computeClass($reflection);
			}
		}

		return $w;
	}
}
