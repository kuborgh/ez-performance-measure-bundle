<?php
/*
 * This file is part of the <PROJECT> package.
 *
 * (c) Kuborgh GmbH
 *
 * For the full copyright and license information, please view the LICENSE-IMPRESS
 * file that was distributed with this source code.
 */

namespace Kuborgh\Bundle\MeasureBundle\Services\LoadContentType\Measurer;

use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\ValueObject;
use Kuborgh\Bundle\MeasureBundle\Services\LoadContentType\AbstractMeasurer;
use eZ\Publish\API\Repository\Values\Content\Query;

class SearchServiceFindSingleMeasurer extends AbstractMeasurer {

    /**
     * Load the given value object.
     * Return the time needed to load the object in ms
     *
     * @param ValueObject $valueObject
     *
     * @return float
     */
    public function measure(ValueObject $valueObject)
    {
        // measure load call
        $start = microtime(true);
        $this->load($valueObject);
        return microtime(true) - $start;
    }

    /**
     * Load the element via ContentService::loadContent.
     *
     * @param ValueObject $valueObject
     */
    private function load(ValueObject $valueObject)
    {
        $query = new Query();
        $query->filter = new Query\Criterion\ContentId($valueObject->id);

        $this->getApiRepository()->getSearchService()->findSingle($query->filter);
    }

    /**
     * Get a name for the result
     *
     * @return string
     */
    public function getName()
    {
        return "SearchService::findSingle (ID -> Object)";
    }

}