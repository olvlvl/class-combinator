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
 * Filter classes to keep only the new classes loaded by the runner.
 */
class ClassDiscriminator
{
	/**
	 * @param callable $runner
	 *
	 * @return string[]
	 */
	public function __invoke(callable $runner)
	{
		$initial_classes = get_declared_classes();
		$initial_traits = get_declared_traits();
		$initial_interfaces = get_declared_interfaces();

		$runner();

		$final_classes = get_declared_classes();
		$final_traits = get_declared_traits();
		$final_interfaces = get_declared_interfaces();

		$loaded_classes = array_diff($final_classes, $initial_classes);
		$loaded_traits = array_diff($final_traits, $initial_traits);
		$loaded_interfaces = array_diff($final_interfaces, $initial_interfaces);

		return array_merge($loaded_interfaces, $loaded_traits, $loaded_classes);
	}
}
