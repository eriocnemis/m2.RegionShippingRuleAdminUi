<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\SaveRuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\ResolveRuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\SaveRuleDataInterface;

/**
 * Save rule data
 *
 * @api
 */
class SaveRuleData implements SaveRuleDataInterface
{
    /**
     * @var SaveRuleInterface
     */
    private $saveRule;

    /**
     * @var ResolveRuleInterface
     */
    private $resolveRule;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * Initialize controller
     *
     * @param SaveRuleInterface $saveRule
     * @param ResolveRuleInterface $resolveRule
     * @param DataPersistorInterface $dataPersistor
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        SaveRuleInterface $saveRule,
        ResolveRuleInterface $resolveRule,
        DataPersistorInterface $dataPersistor,
        MessageManagerInterface $messageManager
    ) {
        $this->saveRule = $saveRule;
        $this->resolveRule = $resolveRule;
        $this->dataPersistor = $dataPersistor;
        $this->messageManager = $messageManager;
    }

    /**
     * Save data
     *
     * @param RequestInterface $request
     * @return RuleInterface
     * @throws LocalizedException
     */
    public function execute(RequestInterface $request): RuleInterface
    {
        $data = $request->getPost('rule');
        if (empty($data)) {
            throw new LocalizedException(
                __('Wrong request.')
            );
        }

        $ruleId = $data[RuleInterface::RULE_ID] ?? null;
        $this->dataPersistor->set('eriocnemis_region_shipping_rule', $data);

        $rule = $this->saveRule->execute(
            $this->resolveRule->execute($ruleId, $data)
        );

        $this->messageManager->addSuccessMessage(
            (string)__('The Rule has been saved.')
        );

        return $rule;
    }
}
