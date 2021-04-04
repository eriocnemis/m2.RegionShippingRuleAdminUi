<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Methods access source
 */
class MethodsAccess implements OptionSourceInterface
{
    /**
     * Retrieve options as array
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Custom')],
            ['value' => 1, 'label' => __('All')]
        ];
    }
}
