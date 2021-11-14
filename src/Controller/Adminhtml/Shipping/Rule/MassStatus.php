<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Controller\Adminhtml\Shipping\Rule;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Mass status controller
 */
class MassStatus extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::shipping_rule_edit';

    /**
     * @var string
     */
    protected $errorMessage = 'We can\'t change status these rules right now. Please review the log and try again.';

    /**
     * Process to collection items
     *
     * @param AbstractDb $collection
     * @return ResultInterface
     */
    protected function massAction(AbstractDb $collection)
    {
        $status = (int)$this->getRequest()->getParam('status');
        $collection->setDataToAll('status', $status);
        $collection->walk('save');

        $this->messageManager->addSuccessMessage(
            (string)__('A total of %1 record(s) have been modified.', $collection->getSize())
        );
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
