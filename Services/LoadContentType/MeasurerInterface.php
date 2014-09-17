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

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Interface MeasurerInterface
 *
 * @package Kuborgh\Bundle\MeasureBundle\Services\LoadContentType
 */
interface MeasurerInterface {

    /**
     * Make measurements for each ValueObject given.
     * Return an
     *
     * @param ValueObject[] $valueObjects
     *
     * @return Result
     */
    public function measureAll($valueObjects);

    /**
     * Load the given value object.
     * Return the time needed to load the object in ms
     *
     * @param ValueObject $valueObject
     *
     * @return float
     */
    public function measure(ValueObject $valueObject);

    /**
     * Get a new for the result
     *
     * @return string
     */
    public function getName();
}