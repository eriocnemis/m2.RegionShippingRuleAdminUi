<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model;

use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionAdminUi\Model\Region\HydratorInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;

/**
 * Shipping rule hydrator
 */
class HydrateRuleIds implements HydratorInterface
{
    /**
     * Hydrate region attribute values
     *
     * @param RegionInterface $region
     * @param mixed[] $data
     * @return void
     */
    public function hydrate(RegionInterface $region, array $data): void
    {
        $region->getExtensionAttributes()->setShippingRuleIds(
            $this->extract($data)
        );
    }

    /**
     * Extract ids from data
     *
     * @param mixed[] $data
     * @return int[]
     */
    private function extract(array $data)
    {
        $ruleIds = [];
        foreach ($data['shipping_rules'] ?? [] as $rule) {
            $ruleIds[] = $rule[RuleInterface::RULE_ID];
        }
        return $ruleIds;
    }
}
