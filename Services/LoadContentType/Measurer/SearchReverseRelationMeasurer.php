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
class SearchReverseRelationMeasurer extends AbstractMeasurer {

    /**
     * Do auth as admin.
     * Then continue as normal.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject[] $valueObjects
     *
     * @return \Kuborgh\Bundle\MeasureBundle\Services\LoadContentType\Result|void
     */
    public function measureAll($valueObjects)
    {
        $apiRepo = $this->getApiRepository();
        $user = $apiRepo->getUserService()->loadUserByLogin('admin');
        $apiRepo->setCurrentUser($user);

        return parent::measureAll($valueObjects);
    }

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
        $this->loadReverseRelations($content->contentInfo);
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
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $sourceContent
     */
    private function loadReverseRelations($sourceContent)
    {
        $cntSrv = $this->getApiRepository()->getContentService();
        $relations = $cntSrv->loadReverseRelations($sourceContent);
        foreach($relations as $relEntry) {
            $cntSrv->loadContent($relEntry->sourceContentInfo->id);
        }
    }

    /**
     * Get a new for the result
     *
     * @return string
     */
    public function getName()
    {
        return "ContentService::loadReverseRelations && loadContent (Content -> Relation -> content)";
    }
}
