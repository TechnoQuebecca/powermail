<?php

namespace In2code\Powermail\Tests\Unit\Utility;

use In2code\Powermail\Utility\ObjectUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Log\Logger;

/**
 * Class ObjectUtilityTest
 * @coversDefaultClass \In2code\Powermail\Utility\ObjectUtility
 */
class ObjectUtilityTest extends UnitTestCase
{
    /**
     * @return void
     * @test
     * @covers ::getFilesArray
     * @covers \In2code\Powermail\Utility\AbstractUtility::getFilesArray
     */
    public function getFilesArray()
    {
        $result = ObjectUtility::getFilesArray();
        self::assertTrue(is_array($result));
    }

    /**
     * @return void
     * @covers ::getLogger
     */
    public function testGetLogger()
    {
        $logger = ObjectUtility::getLogger(self::class);
        self::assertInstanceOf(Logger::class, $logger);
    }
}
