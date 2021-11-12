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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\SaveRuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Api\ResolveRuleInterface;

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
     * @var ResolveRuleInterface
     */
    private $resolveRule;

    /**
     * @var SaveRuleInterface
     */
    private $saveRule;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param ResolveRuleInterface $resolveRule
     * @param SaveRuleInterface $saveRule
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ResolveRuleInterface $resolveRule,
        SaveRuleInterface $saveRule,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger
    ) {
        $this->resolveRule = $resolveRule;
        $this->saveRule = $saveRule;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;

        parent::__construct(
            $context
        );
    }

    /**
     * Save rule
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $data = $this->getRequest()->getPost('rule');
        $ruleId = $data[RuleInterface::RULE_ID] ?? null;

        /** @var Redirect $result */
        $result = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->isPost() || empty($data)) {
            $this->messageManager->addErrorMessage(
                (string)__('Wrong request.')
            );
            $this->redirectAfterFailure($result);
            return $result;
        }

        try {
            $this->dataPersistor->set('eriocnemis_region_shipping_rule', $data);
            $rule = $this->resolveRule->execute($ruleId, $data);
            $rule = $this->saveRule->execute($rule);
            $this->messageManager->addSuccessMessage(
                (string)__('The Rule has been saved.')
            );
            $this->redirectAfterSuccess($result, (int)$rule->getId());
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage(
                    $error->getMessage()
                );
            }
            $this->redirectAfterFailure($result, $ruleId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
            $this->redirectAfterFailure($result, $ruleId);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(
                (string)__('We can\'t save the rule right now. Please review the log and try again.')
            );
            $this->redirectAfterFailure($result, $ruleId);
        }
        return $result;
    }

    /**
     * Retrieve redirect url after save
     *
     * @param Redirect $result
     * @param int $ruleId
     * @return void
     */
    private function redirectAfterSuccess(Redirect $result, $ruleId): void
    {
        $path = '*/*/';
        $params = [];
        if ($this->getRequest()->getParam('back')) {
            $path = '*/*/edit';
            $params = ['_current' => true, RuleInterface::RULE_ID => $ruleId];
        } elseif ($this->getRequest()->getParam('redirect_to_new')) {
            $path = '*/*/new';
            $params = ['_current' => true];
        }
        $result->setPath($path, $params);
    }

    /**
     * Retrieve redirect url after unsuccessful save
     *
     * @param Redirect $result
     * @param int|null $ruleId
     * @return void
     */
    private function redirectAfterFailure(Redirect $result, $ruleId = null): void
    {
        if (null === $ruleId) {
            $result->setPath('*/*/new');
        } else {
            $result->setPath(
                '*/*/edit',
                [RuleInterface::RULE_ID => $ruleId, '_current' => true]
            );
        }
    }
}
