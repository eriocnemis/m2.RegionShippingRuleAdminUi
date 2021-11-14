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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\DeleteRuleByIdInterface;

/**
 * Delete controller
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::shipping_rule_delete';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'delete';

    /**
     * @var DeleteRuleByIdInterface
     */
    private $deleteRuleById;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param DeleteRuleByIdInterface $deleteRuleById
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        DeleteRuleByIdInterface $deleteRuleById,
        ResolveExceptionInterface $resolveException
    ) {
        $this->deleteRuleById = $deleteRuleById;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Delete specified rule
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $ruleId = (int)$this->getRequest()->getPost(RuleInterface::RULE_ID);
        /** @var \Magento\Framework\Controller\Result\Redirect $result */
        $result = $this->resultRedirectFactory->create();

        if ($ruleId) {
            try {
                $this->deleteRuleById->execute($ruleId);
                $this->messageManager->addSuccessMessage(
                    (string)__('The Rule has been deleted.')
                );
                return $result->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->resolveException->execute($e, self::ACTION_NAME);
            }
            return $result->setPath('*/*/edit', $this->getParams($ruleId));
        }
        return $result->setPath('*/*');
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
