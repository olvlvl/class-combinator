<?php

namespace olvlvl\ClassCombinator;

/**
 * Provides classes and automatically add their ancillary classes.
 */
class ClassProvider
{
	/**
	 * @var array
	 */
	private $classes;

	/**
	 * @var ClassDiscriminator
	 */
	private $discriminator;

	/**
	 * @param array $classes
	 * @param ClassDiscriminator|null $discriminator
	 */
	public function __construct(array $classes, ClassDiscriminator $discriminator = null)
	{
		$this->classes = $classes;
		$this->discriminator = $discriminator ?: new ClassDiscriminator(function () {
			foreach ($this->classes as $class_name) {
				class_exists($class_name, true);
			}
		});
	}

	/**
	 * @return string[] An array of class names.
	 */
	public function __invoke(): array
	{
		return ($this->discriminator)();
	}
}
