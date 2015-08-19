<?php
/*
 * This file is part of the <PROJECT> package.
 *
 * (c) Kuborgh GmbH
 *
 * For the full copyright and license information, please view the LICENSE-IMPRESS
 * file that was distributed with this source code.
 */

namespace Kuborgh\Bundle\MeasureBundle\Services\LoadContentType;

use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\Repository\SearchService;
use eZ\Publish\API\Repository\Values\ValueObject;

class SingleManager {

    /**
     * @var eZRepository
     */
    private $apiRepository;

    /**
     * @var MeasurerInterface[]
     */
    private $measurerList = array();

    /**
     * @param \eZ\Publish\API\Repository\Repository $apiRepository
     */
    public function setApiRepository($apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }

    /**
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getApiRepository()
    {
        return $this->apiRepository;
    }

    /**
     * Return search service
     *
     * @return SearchService
     */
    protected function getSearchService()
    {
        return $this->getApiRepository()->getSearchService();
    }

    /**
     * @param MeasurerInterface $measurer
     */
    public function addMeasurer(MeasurerInterface $measurer)
    {
        $this->measurerList[] = $measurer;
    }

    /**
     * @return MeasurerInterface[]
     */
    public function getMeasurerList()
    {
        return $this->measurerList;
    }

    /**
     * Load value objects for the measurer to load.
     * Then run all injected measurers.
     * Returns an array of Results
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return Result[]
     */
    public function run($query)
    {
        // load value objects to search for
        $searchResult = $this->getSearchService()->findContent($query);
        $valueObjects = array();
        foreach($searchResult->searchHits as $hit) {
            $valueObjects[] = $hit->valueObject;
        }

        // for each injected measurer
        $resultSet = array();
        foreach($this->measurerList as $measurer) {
            $resultSet[] = $measurer->measureAll($valueObjects);
        }

        return $resultSet;
    }
}
