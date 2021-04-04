<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\DataProvider\Rule\Modifier\Form;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Shipping\Model\Config\Source\Allmethods as ShippingMethodsSource;
use Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source\MethodsAccess as MethodsAccessSource;

/**
 * Shipping modifier
 *
 * @api
 */
class Shipping implements ModifierInterface
{
    /**
     * Form name
     */
    const FORM = 'eriocnemis_region_shipping_rule_form';

    /**
     * @var ShippingMethodsSource
     */
    private $shippingMethodsSource;

    /**
     * @var MethodsAccessSource
     */
    private $methodsAccessSource;

    /**
     * Initialize modifier
     *
     * @param ShippingMethodsSource $shippingMethodsSource
     * @param MethodsAccessSource $methodsAccessSource
     */
    public function __construct(
        ShippingMethodsSource $shippingMethodsSource,
        MethodsAccessSource $methodsAccessSource
    ) {
        $this->shippingMethodsSource = $shippingMethodsSource;
        $this->methodsAccessSource = $methodsAccessSource;
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
        $meta['shipping']['children'] = [
            'methods_access' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->methodsAccessSource->toOptionArray(),
                            'switcherConfig' => $this->getSwitcherConfig()
                        ]
                    ]
                ]
            ],
            'shipping_methods' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->shippingMethodsSource->toOptionArray()
                        ]
                    ]
                ]
            ]
        ];
        return $meta;
    }

    /**
     * Retrieve switcher config
     *
     * @return mixed[]
     */
    private function getSwitcherConfig()
    {
        return [
            'rules' => [
                $this->getShippingMethodRule('0', 'show'),
                $this->getShippingMethodRule('1', 'hide')
            ],
            'enabled' => true
        ];
    }

    /**
     * Retrieve shipping method rule
     *
     * @param string $value
     * @param string $callback
     * @return mixed[]
     */
    private function getShippingMethodRule($value, $callback)
    {
        return [
            'value' => $value,
            'actions' => [
                $this->getShippingMethodAction($callback)
            ]
        ];
    }

    /**
     * Retrieve shipping method action
     *
     * @param string $callback
     * @return mixed[]
     */
    private function getShippingMethodAction($callback)
    {
        return [
            'target' => $this->getShippingTarget('shipping_methods'),
            'callback' => $callback
        ];
    }

    /**
     * Retrieve shipping target field path
     *
     * @param string $field
     * @return string
     */
    private function getShippingTarget($field)
    {
        return self::FORM . '.' . self::FORM . '.shipping.' . $field;
    }
}
