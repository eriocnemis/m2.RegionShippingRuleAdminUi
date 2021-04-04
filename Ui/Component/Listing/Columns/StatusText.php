<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source\Status as StatusSource;

/**
 * Status column text
 */
class StatusText extends Column
{
    /**
     * @var StatusSource
     */
    protected $statusSource;

    /**
     * Initialize column
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StatusSource $statusSource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StatusSource $statusSource,
        array $components = [],
        array $data = []
    ) {
        $this->statusSource = $statusSource;

        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * Prepare data source
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $sourceFieldName = RuleInterface::STATUS;

        $options = $this->statusSource->toArray();
        foreach ($dataSource['data']['items'] as &$item) {
            if (isset($item[$sourceFieldName])) {
                $item[$fieldName] = $options[$item[$sourceFieldName]];
            }
        }
        return $dataSource;
    }
}
