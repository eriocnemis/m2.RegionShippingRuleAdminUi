<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\DataProvider\Region\Modifier\Form;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Filter\TruncateFilter as Filter;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Eriocnemis\RegionShippingRuleApi\Api\Data\RuleInterface;
use Eriocnemis\RegionShippingRuleApi\Api\GetRuleListInterface;
use Eriocnemis\RegionShippingRuleAdminUi\Model\System\Config\Source\Status as StatusSource;

/**
 * Shipping modifier
 *
 * @api
 */
class Shipping implements ModifierInterface
{
    /**
     * Extension attributes key
     */
    private const EXTENSION_ATTRIBUTES = ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY;

    /**
     * Extension attribute name
     */
    private const ATTRIBUTE = 'shipping_rule_ids';

    /**
     * Form name
     */
    private const FORM = 'eriocnemis_region_form';

    /**
     * Fieldset name
     */
    private const FIELDSET = 'shipping';

    /**
     * Field name
     */
    private const FIELD = 'shipping_rules';

    /**
     * @var GetRuleListInterface
     */
    private $getRuleList;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StatusSource
     */
    protected $statusSource;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var mixed[]
     */
    private $websites;

    /**
     * Initialize modifier
     *
     * @param GetRuleListInterface $getRuleList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param StatusSource $statusSource
     * @param HydratorInterface $hydrator
     * @param Filter $filter
     */
    public function __construct(
        GetRuleListInterface $getRuleList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        StatusSource $statusSource,
        HydratorInterface $hydrator,
        Filter $filter
    ) {
        $this->getRuleList = $getRuleList;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->statusSource = $statusSource;
        $this->hydrator = $hydrator;
        $this->filter = $filter;

        $this->prepareWebsites();
    }

    /**
     * Modify form data
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    public function modifyData(array $data)
    {
        $data[self::FIELD] = [];
        if (!empty($data[self::EXTENSION_ATTRIBUTES])) {
            /** @var mixed[] $extensionAttributes */
            $extensionAttributes = $data[self::EXTENSION_ATTRIBUTES];
            $ruleIds = $extensionAttributes[self::ATTRIBUTE] ?? [];
            if ($ruleIds) {
                /* load related rules */
                $this->searchCriteriaBuilder->addFilter(RuleInterface::RULE_ID, $ruleIds, 'in');
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $searchResult = $this->getRuleList->execute($searchCriteria);
                if (0 < $searchResult->getTotalCount()) {
                    $items = [];
                    foreach ($searchResult->getItems() as $item) {
                        $items[] = $this->hydrator->extract($item);
                    }
                    /* prepare status field */
                    $data[self::FIELD] = array_map([$this, 'updateItem'], $items);
                }
            }
        }
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
        $meta[self::FIELDSET]['children'] = [
            'shipping_rule_button_set' => $this->getButtonSet(),
            'shipping_rule_modal' => $this->getModal(),
            self::FIELD => $this->getDynamicRows()
        ];
        return $meta;
    }

    /**
     * Modify item data
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    public function updateItem(array $data)
    {
        $data = $this->updateStatus($data);
        $data = $this->updateDescription($data);
        $data = $this->updateWebsiteIds($data);

        return $data;
    }

    /**
     * Modify status field
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    private function updateStatus(array $data)
    {
        $options = $this->statusSource->toArray();
        if (isset($data[RuleInterface::STATUS]) && isset($options[$data[RuleInterface::STATUS]])) {
            $data[RuleInterface::STATUS] = $options[$data[RuleInterface::STATUS]];
        }
        return $data;
    }

    /**
     * Modify description field
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    private function updateDescription(array $data)
    {
        if (isset($data[RuleInterface::DESCRIPTION])) {
            $data[RuleInterface::DESCRIPTION] = $this->filter->filter($data[RuleInterface::DESCRIPTION])->getValue();
        }
        return $data;
    }

    /**
     * Modify website ids field
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    private function updateWebsiteIds(array $data)
    {
        if (isset($data[RuleInterface::WEBSITE_IDS])) {
                $websites = [];
                foreach ($data[RuleInterface::WEBSITE_IDS] as $websiteId) {
                    if (isset($this->websites[$websiteId])) {
                        $websites[] = $this->websites[$websiteId];
                    }
                }
                $data[RuleInterface::WEBSITE_IDS] = implode(', ', $websites);
        }
        return $data;
    }

    /**
     * Prepare websites data
     *
     * @return void
     */
    private function prepareWebsites()
    {
        if (null === $this->websites) {
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->websites[$website->getWebsiteId()] = $website->getName();
            }
        }
    }

    /**
     * Retrieve button set
     *
     * @return mixed[]
     */
    private function getButtonSet()
    {
        return [
            'arguments' => $this->getArguments($this->getButtonSetArguments()),
            'children' => ['shipping_rule_button' => $this->getButton()]
        ];
    }

    /**
     * Retrieve button
     *
     * @return mixed[]
     */
    private function getButton()
    {
        return ['arguments' => $this->getArguments($this->getButtonArguments())];
    }

    /**
     * Retrieve save button
     *
     * @return mixed[]
     */
    private function getSaveButton()
    {
        return [
            'text' => __('Add Selected Rules'),
            'class' => 'action-primary',
            'actions' => [$this->getSaveAction(), 'closeModal']
        ];
    }

    /**
     * Retrieve cancel button
     *
     * @return mixed[]
     */
    private function getCancelButton()
    {
        return [
            'text' => __('Cancel'),
            'actions' => ['actionCancel']
        ];
    }

    /**
     * Retrieve modal
     *
     * @return mixed[]
     */
    private function getModal()
    {
        return [
            'arguments' => $this->getArguments($this->getModalArguments()),
            'children' => [$this->getListingTarget() => $this->getListing()]
        ];
    }

    /**
     * Retrieve relation listing
     *
     * @return mixed[]
     */
    private function getListing()
    {
        return ['arguments' => $this->getArguments($this->getListingArguments())];
    }

    /**
     * Retrieve dynamic rows
     *
     * @return mixed[]
     */
    private function getDynamicRows()
    {
        return ['arguments' => $this->getArguments($this->getDynamicRowsArguments())];
    }

    /**
     * Retrieve modal target
     *
     * @return string
     */
    private function getModalTarget()
    {
        return self::FORM . '.' . self::FORM . '.' . self::FIELDSET . '.shipping_rule_modal';
    }

    /**
     * Retrieve listing target
     *
     * @return string
     */
    private function getListingTarget()
    {
        return 'eriocnemis_region_relation_shipping_rule_listing';
    }

    /**
     * Retrieve button set arguments
     *
     * @return mixed[]
     */
    private function getButtonSetArguments()
    {
        $content = __(
            'A shipping method will appear in the list of available methods if at least one rule' .
            ' for it is triggered. Do not add rules here if you need all methods for this region' .
            ' to be determined by the system configuration.'
        );
        return ['content' => $content];
    }

    /**
     * Retrieve button arguments
     *
     * @return mixed[]
     */
    private function getButtonArguments()
    {
        $modalTarget = $this->getModalTarget();
        return [
            'actions' => [
                $this->getOpenModalAction($modalTarget),
                $this->getRenderAction($modalTarget)
            ]
        ];
    }

    /**
     * Retrieve modal arguments
     *
     * @return mixed[]
     */
    private function getModalArguments()
    {
        return [
            'options' => [
                'buttons' => [
                    $this->getCancelButton(),
                    $this->getSaveButton()
                ]
            ]
        ];
    }

    /**
     * Retrieve relation listing arguments
     *
     * @return mixed[]
     */
    private function getListingArguments()
    {
        $listingTarget = $this->getListingTarget();
        return [
            'dataScope' => $listingTarget,
            'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
            'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.' . $listingTarget . '_columns.ids',
            'ns' => $listingTarget
        ];
    }

    /**
     * Retrieve dynamic rows arguments
     *
     * @return mixed[]
     */
    private function getDynamicRowsArguments()
    {
        return [
            'template' => 'ui/dynamic-rows/templates/grid',
            'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
            'dataProvider' => $this->getListingTarget()
        ];
    }

    /**
     * Retrieve arguments data
     *
     * @param mixed[] $config
     * @return mixed[]
     */
    private function getArguments(array $config)
    {
        return ['data' => ['config' => $config]];
    }

    /**
     * Retrieve open modal action
     *
     * @param string $modalTarget
     * @return mixed[]
     */
    private function getOpenModalAction($modalTarget)
    {
        return [
            'targetName' => $modalTarget,
            'actionName' => 'openModal'
        ];
    }

    /**
     * Retrieve render action
     *
     * @param string $modalTarget
     * @return mixed[]
     */
    private function getRenderAction($modalTarget)
    {
        return [
            'targetName' => $modalTarget . '.' . $this->getListingTarget(),
            'actionName' => 'render'
        ];
    }

    /**
     * Retrieve save action
     *
     * @return mixed[]
     */
    private function getSaveAction()
    {
        return [
            'targetName' => 'index = ' . $this->getListingTarget(),
            'actionName' => 'save'
        ];
    }
}
