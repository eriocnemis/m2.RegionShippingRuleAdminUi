<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Controller\Adminhtml\Shipping\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\ValidateRuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\ResolveRuleInterface;

/**
 * Validate rule
 */
class Validate extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::shipping_rule_edit';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'validate';

    /**
     * @var ResolveRuleInterface
     */
    private $resolveRule;

    /**
     * @var ValidateRuleInterface
     */
    private $validateRule;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ResolveRuleInterface $resolveRule
     * @param ValidateRuleInterface $validateRule
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResolveRuleInterface $resolveRule,
        ValidateRuleInterface $validateRule,
        ResolveExceptionInterface $resolveException
    ) {
        $this->resolveRule = $resolveRule;
        $this->validateRule = $validateRule;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Validate rule
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $response = ['error' => true];
        $data = $this->getRequest()->getPost('rule');
        $ruleId = $data[RuleInterface::RULE_ID] ?? null;

        try {
            $rule = $this->resolveRule->execute($ruleId, $data);
            $this->validateRule->execute($rule);
            $response = ['error' => false];
        } catch (\Exception $e) {
            $this->resolveException->execute($e, self::ACTION_NAME);
            $response['messages'] = [];
            foreach ($this->messageManager->getMessages(true)->getErrors() as $message) {
                $response['messages'][] = $message->getText();
            }
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
