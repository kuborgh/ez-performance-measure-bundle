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

/**
 * Measurement result object.
 * Simple container for measurements results
 *
 * @package Kuborgh\Bundle\MeasureBundle\Services\LoadContentType
 */
class Result {

    /**
     * Minimum load time
     *
     * @var float
     */
    private $min;

    /**
     * Maximum load time
     *
     * @var float
     */
    private $max;

    /**
     * Average load time
     *
     * @var float
     */
    private $avg;

    /**
     * Minimum load time percentage
     *
     * @var float
     */
    private $minPercentage;

    /**
     * Maximum load time percentage
     *
     * @var float
     */
    private $maxPercentage;

    /**
     * Average load time percentage
     *
     * @var float
     */
    private $avgPercentage;

    /**
     * Iterations performed
     *
     * @var int
     */
    private $iterations;

    /**
     * Human readable reference to measurer
     *
     * @var string
     */
    private $reference;

    /**
     * @param float $avg
     */
    public function setAvg($avg)
    {
        $this->avg = $avg;
    }

    /**
     * @param float $avgPercentage
     */
    public function setAvgPercentage($avgPercentage)
    {
        $this->avgPercentage = $avgPercentage;
    }

    /**
     * @return float
     */
    public function getAvg()
    {
        return $this->avg;
    }

    /**
     * @return float
     */
    public function getAvgPercentage()
    {
        return $this->avgPercentage;
    }

    /**
     * @param int $iterations
     */
    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
    }

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * @param float $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @param float $maxPercentage
     */
    public function setMaxPercentage($maxPercentage)
    {
        $this->maxPercentage = $maxPercentage;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return float
     */
    public function getMaxPercentage()
    {
        return $this->maxPercentage;
    }

    /**
     * @param float $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @param float $minPercentage
     */
    public function setMinPercentage($minPercentage)
    {
        $this->minPercentage = $minPercentage;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMinPercentage()
    {
        return $this->minPercentage;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}