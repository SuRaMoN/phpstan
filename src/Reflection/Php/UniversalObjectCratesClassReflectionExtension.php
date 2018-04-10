<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Php;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;

class UniversalObjectCratesClassReflectionExtension
	implements \PHPStan\Reflection\PropertiesClassReflectionExtension, \PHPStan\Reflection\BrokerAwareExtension
{

	/** @var string[] */
	private $classes;

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/**
	 * @param string[] $classes
	 */
	public function __construct(array $classes)
	{
		$this->classes = $classes;
	}

	public function setBroker(Broker $broker): void
	{
		$this->broker = $broker;
	}

	public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
	{
		foreach ($this->classes as $className) {
			if (!$this->broker->hasClass($className)) {
				continue;
			}
			if (
				$classReflection->getName() === $className
				|| $classReflection->isSubclassOf($className)
			) {
				return true;
			}
		}

		return false;
	}

	public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
	{
		return new UniversalObjectCrateProperty($classReflection);
	}

}
