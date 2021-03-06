<?php
/*
 * This file is part of the <PROJECT> package.
 *
 * (c) Kuborgh GmbH
 *
 * For the full copyright and license information, please view the LICENSE-IMPRESS
 * file that was distributed with this source code.
 */

namespace Kuborgh\Bundle\MeasureBundle\Command;


use eZ\Publish\API\Repository\Values\Content\Query;
use Kuborgh\Bundle\MeasureBundle\Services\LoadContentType\Result;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PerformanceListCommand extends ContainerAwareCommand {

    const ARGUMENT_CONTENT_TYPE = 'ctype';
    const OPTION_ITERATIONS = 'iterations';
    const OUTPUT_OPTIONS = 'show_min_max';
    const OPTION_USER = 'as_user';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('kb:measure:performance_list');
        $this->setDescription('Execute performance tests for the given content type and print result.');
        $this->addArgument(self::ARGUMENT_CONTENT_TYPE, null, 'eZ Content Type');
        $this->addOption(self::OPTION_ITERATIONS, 'iter', InputOption::VALUE_OPTIONAL, 'Amount of content objects to load and measure', 100);
        $this->addOption(self::OUTPUT_OPTIONS, 'minmax', InputOption::VALUE_OPTIONAL, 'Should min / max values be shown', 0);
        $this->addOption(self::OPTION_USER, 'user', InputOption::VALUE_OPTIONAL, 'User ID to run tests with', 0);
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument(self::ARGUMENT_CONTENT_TYPE);
        $iterations = (int)$input->getOption(self::OPTION_ITERATIONS);
        $showMinMax = $input->getOption(self::OUTPUT_OPTIONS);
        $userId = (int)$input->getOption(self::OPTION_USER);

        if(!$type) {
            $output->writeln('No Content type given. Abort.');
            return;
        }

        $manager = $this->getMeasureManager();

        $measurerNames = array();
        foreach($manager->getMeasurerList() as $measurer) {
            $measurerNames[] = $measurer->getName();
        }

        $output->writeln(sprintf("Running max. %d tests for %s with measurers %s\n...", $iterations, $type, implode(', ', $measurerNames)));
        $resultSet = $manager->run($type, $iterations, $userId);

        $output->write("\n<info>Results</info>\n\n");

        $output->writeln(sprintf("Iterations:\t%d", $iterations));

        // get maximum values for min/max/avg
        $minimumArray = array("min"=>999999, "max"=>999999, "avg"=>99999999);
        foreach($resultSet as $result) {
            $currentMin = $result->getMin();
            $currentMax = $result->getMax();
            $currentAvg = $result->getAvg();

            // set the minimum to the lowest minimum value
            $minimumArray['min'] = min($currentMin, $minimumArray['min']);
            $minimumArray['max'] = min($currentMax, $minimumArray['max']);
            $minimumArray['avg'] = min($currentAvg, $minimumArray['avg']);
        }

        // set average to each
        foreach($resultSet as $result) {
            $result->setMinPercentage(empty($minimumArray['min']) ? 0 : round($result->getMin()/$minimumArray['min']*100,2));
            $result->setMaxPercentage(empty($minimumArray['max']) ? 0 : round($result->getMax()/$minimumArray['max']*100,2));
            $result->setAvgPercentage(empty($minimumArray['avg']) ? 0 : round($result->getAvg()/$minimumArray['avg']*100,2));
        }


        // output
        foreach($resultSet as $result) {
            $this->printResult($result, $output, $showMinMax);
        }
    }

    /**
     * Print the result
     *
     * @param Result $result
     * @param OutputInterface $output
     * @param bool $showMinMax
     */
    protected function printResult(Result $result, OutputInterface $output, $showMinMax = false)
    {
        $output->writeln(sprintf("\nResult for:\t%s", $result->getReference()));
        if ($showMinMax) {
            $output->writeln(sprintf("Min. time:\t%01.3f ms\t%01.0f%%", $result->getMin()*1000, $result->getMinPercentage()));
            $output->writeln(sprintf("Max. time:\t%01.3f ms\t%01.0f%%", $result->getMax()*1000, $result->getMaxPercentage()));
        }
        $output->writeln(sprintf("Avg. time:\t%01.3f ms\t%01.0f%%", $result->getAvg()*1000, $result->getAvgPercentage()));
    }

    /**
     * @return \Kuborgh\Bundle\MeasureBundle\Services\LoadContentType\Manager
     */
    protected function getMeasureManager()
    {
        return $this->getContainer()->get('kuborgh_measure.listservice.contenttypeload');
    }
} 