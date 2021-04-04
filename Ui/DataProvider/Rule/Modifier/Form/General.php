<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\DataProvider\Rule\Modifier\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Config\Model\Config\Source\Website as WebsiteSource;

/**
 * General modifier
 *
 * @api
 */
class General implements ModifierInterface
{
    /**
     * @var WebsiteSource
     */
    private $websiteSource;

    /**
     * Initialize modifier
     *
     * @param WebsiteSource $websiteSource
     */
    public function __construct(
        WebsiteSource $websiteSource
    ) {
        $this->websiteSource = $websiteSource;
    }

    /**
     * Modify form data
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify form meta
     *
     * @param mixed[] $meta
     * @return mixed[]
     */
    public function modifyMeta(array $meta)
    {
        $meta['general']['children']['status'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'prefer' => 'toggle',
                        'valueMap' => ['false' => '0', 'true' => '1']
                    ]
                ]
            ]
        ];

        $meta['general']['children']['website_ids'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'options' => $this->websiteSource->toOptionArray()
                    ]
                ]
            ]
        ];

        return $meta;
    }
}
