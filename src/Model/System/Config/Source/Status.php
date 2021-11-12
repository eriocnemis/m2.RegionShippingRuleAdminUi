<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Status source
 */
class Status implements OptionSourceInterface
{
    /**
     * Value which equal Enable for Status dropdown
     */
    const ENABLE_VALUE = 1;

    /**
     * Value which equal Disable for Status dropdown
     */
    const DISABLE_VALUE = 0;

    /**
     * Retrieve options as array
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ENABLE_VALUE, 'label' => __('Enable')],
            ['value' => self::DISABLE_VALUE, 'label' => __('Disable')],
        ];
    }

    /**
     * Retrieve options in key-value format
     *
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            self::ENABLE_VALUE => __('Enable'),
            self::DISABLE_VALUE => __('Disable')
        ];
    }
}
