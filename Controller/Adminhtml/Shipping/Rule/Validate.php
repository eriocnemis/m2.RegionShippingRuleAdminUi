<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Controller\Adminhtml\Shipping\Rule;

use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationException;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ResolveRuleInterface $resolveRule
     * @param ValidateRuleInterface $validateRule
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResolveRuleInterface $resolveRule,
        ValidateRuleInterface $validateRule,
        LoggerInterface $logger
    ) {
        $this->resolveRule = $resolveRule;
        $this->validateRule = $validateRule;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;

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
        } catch (ValidationException $e) {
            $response['messages'] = [];
            foreach ($e->getErrors() as $error) {
                $response['messages'][] = $error->getMessage();
            }
        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $response['message'] = __('We can\'t validate the rule right now. Please review the log and try again.');
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
