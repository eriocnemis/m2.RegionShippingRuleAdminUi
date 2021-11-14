<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Controller\Adminhtml\Shipping\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\SaveRuleDataInterface;

/**
 * Save controller
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::shipping_rule_edit';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'save';

    /**
     * @var SaveRuleDataInterface
     */
    private $saveRuleData;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param SaveRuleDataInterface $saveRuleData
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        SaveRuleDataInterface $saveRuleData,
        ResolveExceptionInterface $resolveException
    ) {
        $this->saveRuleData = $saveRuleData;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Save rule
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $ruleId = (int)$this->getRequest()->getPost(RuleInterface::RULE_ID);
        /** @var Redirect $result */
        $result = $this->resultRedirectFactory->create();

        try {
            $rule = $this->saveRuleData->execute($this->getRequest());
            return $this->resolveResult($result, (int)$rule->getId());
        } catch (\Exception $e) {
            $this->resolveException->execute($e, self::ACTION_NAME);
        }
        return $this->resolveFailureResult($result, $ruleId);
    }

    /**
     * Resolve success result
     *
     * @param Redirect $result
     * @param int $ruleId
     * @return ResultInterface
     */
    private function resolveResult(Redirect $result, int $ruleId): ResultInterface
    {
        return empty($this->getRequest()->getParam('back'))
            ? $result->setPath('*/*/index')
            : $result->setPath('*/*/edit', $this->getParams($ruleId));
    }

    /**
     * Resolve failure result
     *
     * @param Redirect $result
     * @param int|null $ruleId
     * @return ResultInterface
     */
    private function resolveFailureResult(Redirect $result, int $ruleId = null): ResultInterface
    {
        return empty($ruleId)
            ? $result->setPath('*/*/new')
            : $result->setPath('*/*/edit', $this->getParams($ruleId));
    }

    /**
     * Retrieve params
     *
     * @param int $ruleId
     * @return mixed[]
     */
    private function getParams(int $ruleId): array
    {
        return [
            RuleInterface::RULE_ID => $ruleId,
            '_current' => true
        ];
    }
}
