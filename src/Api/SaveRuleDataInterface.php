<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;

/**
 * Save data
 *
 * @api
 */
interface SaveRuleDataInterface
{
    /**
     * Save data
     *
     * @param RequestInterface $request
     * @return RuleInterface
     * @throws LocalizedException
     */
    public function execute(RequestInterface $request): RuleInterface;
}
