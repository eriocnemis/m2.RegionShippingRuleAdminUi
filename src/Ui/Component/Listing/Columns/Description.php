<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Filter\TruncateFilter as Filter;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Description column
 */
class Description extends Column
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * Initialize column
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Filter $filter
     * @param mixed[] $components
     * @param mixed[] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Filter $filter,
        array $components = [],
        array $data = []
    ) {
        $this->filter = $filter;

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
        foreach ($dataSource['data']['items'] as &$item) {
            if (!empty($item[$fieldName])) {
                $item[$fieldName] = $this->filter->filter($item[$fieldName])->getValue();
            }
        }
        return $dataSource;
    }
}
