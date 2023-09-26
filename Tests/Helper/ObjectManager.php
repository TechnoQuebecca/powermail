<?php

namespace In2code\Powermail\Tests\Helper;

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class ObjectManager
 */
class ObjectManager implements ObjectManagerInterface
{
    /**
     * @return bool
     */
    public function isRegistered(string $objectName): bool
    {
        unset($objectName);
        return true;
    }

    /**
     * @return object
     */
    public function get(string $objectName, mixed ...$constructorArguments): object
    {
        unset($constructorArguments);
        return new $objectName();
    }

    /**
     * @return mixed
     */
    public function getEmptyObject(string $objectName): object
    {
        return $this->get($objectName);
    }

    /**
     * @return int
     */
    public function getScope(string $objectName): int
    {
        unset($objectName);
        return 0;
    }
}
