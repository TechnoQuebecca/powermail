<?php

namespace In2code\Powermail\Tests\Unit\ViewHelpers\Misc;

use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PrefillFieldViewHelperTest
 * @coversDefaultClass \In2code\Powermail\ViewHelpers\Misc\PrefillFieldViewHelper
 */
class PrefillFieldViewHelperTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $abstractValidationViewHelperMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->abstractValidationViewHelperMock = $this->getAccessibleMock(
            PrefillFieldViewHelper::class,
            ['dummy']
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider for getDefaultValueReturnsString()
     *
     * @return array
     */
    public function getDefaultValueReturnsStringDataProvider()
    {
        return [
            [
                [ // field values
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [ // variables from POST
                    'field' => [
                        'marker' => 'abc',
                        '123' => 'ghi',
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl',
                ],
                [ // configuration
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'abc', // expected
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [
                    'field' => [
                        '123' => 'ghi',
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl',
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'def',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [
                    'field' => [
                        '123' => 'ghi',
                    ],
                    'uid123' => 'jkl',
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'mno',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [
                    'uid123' => 'jkl',
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'mno',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'mno',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [],
                [],
                'mno',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                ],
                [],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'pqr',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                    'prefillValue' => 'mno',
                ],
                [
                    'field' => [
                        'marker' => '',
                        '123' => 'ghi',
                    ],
                    'marker' => 'def',
                    'uid123' => 'jkl',
                ],
                [
                    'prefill.' => [
                        'marker' => 'pqr',
                    ],
                ],
                'def',
            ],
            [
                [
                    'uid' => 123,
                    'marker' => 'marker',
                ],
                [],
                [],
                '',
            ],
        ];
    }

    /**
     * @param array $fieldValues
     * @param array $variables
     * @param array $configuration
     * @param string $expectedResult
     * @return void
     * @dataProvider getDefaultValueReturnsStringDataProvider
     * @test
     * @covers ::render
     * @covers ::getValue
     * @covers ::buildValue
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getDefaultValueReturnsString($fieldValues, $variables, $configuration, $expectedResult)
    {
        $field = new Field();
        foreach ($fieldValues as $name => $value) {
            $field->_setProperty($name, $value);
        }
        $this->abstractValidationViewHelperMock->_set('contentObject', new ContentObjectRenderer());
        $this->abstractValidationViewHelperMock->_set('variables', $variables);
        $this->abstractValidationViewHelperMock->_set('configuration', $configuration);
        $this->abstractValidationViewHelperMock->_set('field', $field);
        $this->abstractValidationViewHelperMock->_set('marker', $field->getMarker());
        $this->abstractValidationViewHelperMock->_callRef('buildValue');
        self::assertSame($expectedResult, $this->abstractValidationViewHelperMock->_callRef('getValue'));
    }

    /**
     * Dataprovider for getFromTypoScriptRawReturnsString()
     *
     * @return array
     */
    public function getFromTypoScriptRawReturnsStringDataProvider()
    {
        return [
            [
                [
                    'prefill.' => [
                        'email' => 'abcdef',
                    ],
                ],
                'email',
                'abcdef',
            ],
            [
                [
                    'prefill.' => [
                        'email' => 'TEXT',
                        'email.' => [
                            'value' => 'xyz',
                        ],
                    ],
                ],
                'email',
                '',
            ],
            [
                [
                    'prefill.' => [
                        'marker' => 'TEXT',
                    ],
                ],
                'marker',
                'TEXT',
            ],
        ];
    }

    /**
     * @param string $marker
     * @param string $expectedResult
     * @return void
     * @dataProvider getFromTypoScriptRawReturnsStringDataProvider
     * @test
     * @covers ::getFromTypoScriptRaw
     */
    public function getFromTypoScriptRawReturnsString(array $configuration, $marker, $expectedResult)
    {
        $this->abstractValidationViewHelperMock->_set('configuration', $configuration);
        $this->abstractValidationViewHelperMock->_set('marker', $marker);
        $value = '';
        self::assertSame(
            $expectedResult,
            $this->abstractValidationViewHelperMock->_callRef('getFromTypoScriptRaw', $value)
        );
    }
}
