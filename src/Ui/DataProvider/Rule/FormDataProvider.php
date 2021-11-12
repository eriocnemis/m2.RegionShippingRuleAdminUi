<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionShippingRuleAdminUi\Ui\DataProvider\Rule;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Eriocnemis\RegionShippingRuleApi\Api\GetRuleByIdInterface;

/**
 * Data provider for admin export job form
 *
 * @api
 */
class FormDataProvider extends DataProvider
{
    /**
     * @var GetRuleByIdInterface
     */
    private $getRuleById;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Initialize provider
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param GetRuleByIdInterface $getRuleById
     * @param DataPersistorInterface $dataPersistor
     * @param HydratorInterface $hydrator
     * @param PoolInterface $modifierPool
     * @param mixed[] $meta
     * @param mixed[] $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        GetRuleByIdInterface $getRuleById,
        DataPersistorInterface $dataPersistor,
        HydratorInterface $hydrator,
        PoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        $this->getRuleById = $getRuleById;
        $this->dataPersistor = $dataPersistor;
        $this->modifierPool = $modifierPool;
        $this->hydrator = $hydrator;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Retrieve data
     *
     * @return mixed[]
     */
    public function getData()
    {
        $ruleId = $this->getRuleId();
        if (!isset($this->data[$ruleId])) {
            $this->data[$ruleId]['rule'] = $this->modifyData($this->loadData($ruleId));
        }
        return $this->data;
    }

    /**
     * Retrieve meta data
     *
     * @return mixed[]
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    /**
     * Retrieve rule id
     *
     * @return int|null
     */
    private function getRuleId(): ?int
    {
        $ruleId = $this->request->getParam($this->getRequestFieldName());
        return $ruleId ? (int)$ruleId : null;
    }

    /**
     * Retrieve rule data
     *
     * @param int|null $ruleId
     * @return mixed[]
     */
    private function loadData($ruleId): array
    {
        $data = $this->dataPersistor->get('eriocnemis_region_shipping_rule') ?: [];
        if (null !== $ruleId) {
            $rule = $this->getRuleById->execute($ruleId);
            $data = $this->hydrator->extract($rule);
        }
        $this->dataPersistor->clear('eriocnemis_region_shipping_rule');

        return $data;
    }

    /**
     * Retrieve modifier data
     *
     * @param  mixed[] $data
     * @return mixed[]
     */
    private function modifyData(array $data): array
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }
}
