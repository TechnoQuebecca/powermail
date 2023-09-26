<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

/**
 * Class MathematicUtility
 */
class MathematicUtility
{
    /**
     * Basic mathematic operation with two variables and one operator
     *
     * @param string $operator +|-|x|:
     * @return int
     */
    public static function mathematicOperation(int $number1, int $number2, string $operator = '+'): int
    {
        $result = match ($operator) {
            '-' => $number1 - $number2,
            'x' => $number1 * $number2,
            ':' => $number1 / $number2,
            default => $number1 + $number2,
        };
        return $result;
    }
}
