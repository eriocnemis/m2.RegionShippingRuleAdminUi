<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;

/**
 * Resolve rule data interface
 *
 * @api
 */
interface ResolveRuleInterface
{
    /**
     * Resolve rule
     *
     * @param int|null $ruleId
     * @param mixed[] $data
     * @return RuleInterface
     * @throws NoSuchEntityException
     */
    public function execute($ruleId, array $data): RuleInterface;
}
