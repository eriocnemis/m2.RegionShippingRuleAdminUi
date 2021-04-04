<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterfaceFactory;
use Eriocnemis\RegionShippingRuleApi\Api\GetRuleByIdInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\ResolveRuleInterface;

/**
 * Resolve rule
 *
 * @api
 */
class ResolveRule implements ResolveRuleInterface
{
    /**
     * @var RuleInterfaceFactory
     */
    private $factory;

    /**
     * @var GetRuleByIdInterface
     */
    private $getRuleById;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Initialize provider
     *
     * @param RuleInterfaceFactory $factory
     * @param GetRuleByIdInterface $getRuleById
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        RuleInterfaceFactory $factory,
        GetRuleByIdInterface $getRuleById,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->factory = $factory;
        $this->getRuleById = $getRuleById;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve rule
     *
     * @param int|null $ruleId
     * @param mixed[] $data
     * @return RuleInterface
     * @throws NoSuchEntityException
     */
    public function execute($ruleId, array $data): RuleInterface
    {
        /** @var RuleInterface $rule */
        $rule = null !== $ruleId
            ? $this->getRuleById->execute((int)$ruleId)
            : $this->factory->create();

        if (empty($data[RuleInterface::SHIPPING_METHODS])) {
            $data[RuleInterface::SHIPPING_METHODS] = [];
        }
        $this->dataObjectHelper->populateWithArray($rule, $data, RuleInterface::class);

        return $rule;
    }
}
