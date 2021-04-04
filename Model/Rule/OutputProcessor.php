<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model\Rule;

use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;

/**
 * Format the output result
 */
class OutputProcessor
{
    /**
     * Format the output result
     *
     * @param RuleInterface $rule
     * @param mixed[] $data
     * @return mixed[]
     */
    public function execute(RuleInterface $rule, array $data)
    {
        return array_map([$this, 'updateValue'], $data);
    }

    /**
     * Modify data value
     *
     * @param mixed $value
     * @return mixed
     */
    public function updateValue($value)
    {
        // for proper work of form and grid (for example for Yes/No properties)
        if (is_array($value)) {
            return array_map([$this, 'updateValue'], $value);
        } elseif (is_numeric($value)) {
            $value = (string)$value;
        } elseif (is_bool($value)) {
            $value = (string)(int)$value;
        }
        return $value;
    }
}
