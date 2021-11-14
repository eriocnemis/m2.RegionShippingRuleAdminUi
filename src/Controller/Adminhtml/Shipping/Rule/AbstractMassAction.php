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
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Eriocnemis\RegionShippingRule\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Abstract mass action controller
 */
abstract class AbstractMassAction extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->logger = $logger;

        parent::__construct(
            $context
        );
    }

    /**
     * Execute action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        try {
            $collection = $this->filter->getCollection(
                $this->collectionFactory->create()
            );

            if ($collection->getSize()) {
                return $this->massAction($collection);
            }
            $this->messageManager->addErrorMessage(
                (string)__('Please correct the rules you requested.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(
                (string)__($this->errorMessage)
            );
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Process to collection items
     *
     * @param AbstractDb $collection
     * @return ResultInterface
     */
    abstract protected function massAction(AbstractDb $collection);
}
