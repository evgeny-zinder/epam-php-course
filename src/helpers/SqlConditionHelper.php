<?php

namespace rms\helpers;

/**
 * Class SqlConditionHelper
 * Recursively generates nested SQL WHERE conditions from array
 * 1. Simple condition:
 *    ['field' => 'value'] is converted to 'field = "value"'
 * 2. Condition with logical operation
 *    ['or' => [
 *        ['field1' => 'value1'],
 *        ['field2' => 'value2']
 *    ] is converted to '(field1 = "value1" OR field2 = "value2")
 * 3. Locical conditions can be nested:
 *    ['and' => [
 *         ['or' =>
 *             ['field1' => 'value1'],
 *             ['field1' => 'value2']
 *         ],
 *         ['or' =>
 *             ['field2' => 'value3'],
 *             ['field2' => 'value4']
 *         ],
 *    ] is converted to '((field1 = "value1") OR (field1 = "value2")) AND ((field2 = "value3") OR (field2 = "value4"))
 *
 * @package rms\helpers
 */
class SqlConditionHelper
{
    /**
     * Builds condition string from array
     * @param array $conditions condition(-s) in format described above
     * @return string
     */
    public function build($conditions)
    {
        list($op, $condition) = each($conditions);
        if (in_array(strtolower($op), ['and', 'or'])) {
            return $this->buildCompositeCondition($op, $condition);
        }
        return $this->buildSimpleCondition($conditions);
    }

    /**
     * Generates simple condition string (field = "value")
     * @param array $condition
     * @return string
     */
    private function buildSimpleCondition(array $condition)
    {
        list($key, $value) = each($condition);
        return $this->escapePair($key, $value);
    }

    /**
     * Recursively generates logical-imploded condition string
     * @param string $op "or" or "and" operation name
     * @param array $conditions
     * @return string
     */
    private function buildCompositeCondition($op, array $conditions)
    {
        $result = [];
        foreach ($conditions as $condition) {
            $result[] = $this->build($condition);
        }
        return '(' . implode(' ' . $op . ' ', $result) . ')';
    }

    /**
     * Proceeds with SQL escaping
     * @param string $key field name
     * @param string $value field value
     * @return string
     */
    private function escapePair($key, $value)
    {
        if (is_int($value)) {
            return sprintf('%s = %d', $key, $value);
        }
        if (null === $value) {
            return sprintf('%s = NULL', $key);
        }
        return sprintf('%s = "%s"', $key, $value);
    }
}