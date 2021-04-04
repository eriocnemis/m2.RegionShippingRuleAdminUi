<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source;

use Magento\Framework\Convert\DataObject;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * Customer group source
 */
class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * Initialize source
     *
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
    }

    /**
     * Retrieve options as array
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        $customerGroups = $this->groupRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return $this->objectConverter->toOptionArray($customerGroups, 'id', 'code');
    }
}
