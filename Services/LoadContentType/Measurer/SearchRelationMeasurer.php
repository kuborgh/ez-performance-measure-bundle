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

/**
 * Load a content object.
 * Measure load time for all related content for that object.
 */
class SearchRelationMeasurer extends AbstractMeasurer {

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
        $content = $this->load($valueObject);

        // measure load call
        $start = microtime(true);
        $this->loadRelations($content->versionInfo);
        return microtime(true) - $start;
    }

    /**
     * Load the element via ContentService::loadContent.
     *
     * @param ValueObject $valueObject
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function load(ValueObject $valueObject)
    {
        return $this->getApiRepository()->getContentService()->loadContent($valueObject->id);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $sourceContent
     */
    private function loadRelations($sourceContent)
    {
        $this->getApiRepository()->getContentService()->loadRelations($sourceContent);
    }

    /**
     * Get a new for the result
     *
     * @return string
     */
    public function getName()
    {
        return "ContentService::loadRelations (Content -> Relation)";
    }
}
