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
 * Mass delete controller
 */
class MassDelete extends AbstractMassAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::shipping_rule_delete';

    /**
     * @var string
     */
    protected $errorMessage = 'We can\'t delete these rules right now. Please review the log and try again.';

    /**
     * Process to collection items
     *
     * @param AbstractDb $collection
     * @return ResultInterface
     */
    protected function massAction(AbstractDb $collection)
    {
        $collection->walk('delete');

        $this->messageManager->addSuccessMessage(
            (string)__('You deleted a total of %1 records.', $collection->getSize())
        );
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
