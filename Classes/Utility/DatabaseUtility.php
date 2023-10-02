<?php

declare(strict_types=1);
namespace In2code\Powermail\Utility;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseUtility
 * @codeCoverageIgnore
 */
class DatabaseUtility
{
    /**
     * @return QueryBuilder
     */
    public static function getQueryBuilderForTable(string $tableName, bool $removeRestrictions = false): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        if ($removeRestrictions === true) {
            $queryBuilder->getRestrictions()->removeAll();
        }
        return $queryBuilder;
    }

    /**
     * @return Connection
     */
    public static function getConnectionForTable(string $tableName): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }

    /**
     * @return bool
     * @throws DBALException
     */
    public static function isTableExisting(string $tableName): bool
    {
        $existing = false;
        $connection = self::getConnectionForTable($tableName);
        $queryResult = $connection->query('show tables;')->fetchAll();
        foreach ($queryResult as $tableProperties) {
            if (in_array($tableName, array_values($tableProperties))) {
                $existing = true;
                break;
            }
        }
        return $existing;
    }

    /**
     * @return bool
     * @throws DBALException
     */
    public static function isFieldExistingInTable(string $fieldName, string $tableName): bool
    {
        $found = false;
        $connection = self::getConnectionForTable($tableName);
        $queryResult = $connection->query('describe ' . $tableName . ';')->fetchAll();
        foreach ($queryResult as $fieldProperties) {
            if ($fieldProperties['Field'] === $fieldName) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * Check if there are any values in a table field (don't care about deleted property)
     *
     * @return bool
     * @throws DBALException
     */
    public static function isFieldFilled(string $fieldName, string $tableName): bool
    {
        if (self::isFieldExistingInTable($fieldName, $tableName)) {
            $queryBuilder = self::getQueryBuilderForTable($tableName, true);
            return (int)$queryBuilder
                    ->count($fieldName)
                    ->from($tableName)->where($fieldName . ' != "" and ' . $fieldName . ' != 0')->executeQuery()
                    ->fetchColumn() > 0;
        }
        return false;
    }
}
