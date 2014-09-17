<?php
/**
 * Created by PhpStorm.
 * User: moritz
 * Date: 31.08.14
 * Time: 14:29
 */

namespace Kuborgh\Bundle\MeasureBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\Content\Query;

class AgeContentCommand extends ContainerAwareCommand {

    const ARGUMENT_CONTENT_TYPE = 'content-type';
    const OPTION_VERSIONS = 'versions';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('kb:measure:age');
        $this->setDescription('All content objects of the given content type will be saved to simulate natural aging process.');
        $this->addArgument(self::ARGUMENT_CONTENT_TYPE, null, 'eZ Content Type');
        $this->addOption(self::OPTION_VERSIONS, 'ver', InputOption::VALUE_OPTIONAL, 'Amount of versions to create', 10);
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
        // read user input
        $contentTypeName = $input->getArgument(self::ARGUMENT_CONTENT_TYPE);
        $versions = $input->getOption(self::OPTION_VERSIONS);

        // login
        $this->getEzRepository()->setCurrentUser($this->getEzRepository()->getUserService()->loadUserByLogin('admin'));

        // work
        $output->writeln("Loading...");
        $contents = $this->loadContentObjects($contentTypeName);
        $output->writeln(sprintf("Creating %d versions for each %s and go :", $versions, $contentTypeName));
        foreach($contents as $cnt) {
            $this->age($cnt, $versions);
            $output->write('.');
        }
        $output->writeln('Done');
    }

    /**
     *
     *
     * @param string $contentTypeName
     *
     * @return array
     */
    protected function loadContentObjects($contentTypeName)
    {
        $query = new Query();
        $query->criterion = new Query\Criterion\ContentTypeIdentifier($contentTypeName);

        $searchResult = $this->getSearchService()->findContent($query);
        $contentsToFind = array();
        foreach($searchResult->searchHits as $hit) {
            $contentsToFind[] = $hit->valueObject;
        }

        return $contentsToFind;
    }

    /**
     * Create versions for the given content
     *
     * @param object $content
     * @param int $versions
     */
    protected function age($content, $versions)
    {
        $cntSrvc = $this->getContentService();

        for($i = 0 ; $i < $versions ; $i++) {
            $cntDraft = $cntSrvc->createContentDraft($content->contentInfo);

            // once in the progress publish draft
            if($i == floor($versions / 2)) {
                $content = $cntSrvc->publishVersion($cntDraft->versionInfo);
            }

            // Update publish date
            $cntMetaUpdateStruct = $cntSrvc->newContentMetadataUpdateStruct();
            $cntMetaUpdateStruct->publishedDate = new \DateTime();
            $cntSrvc->updateContentMetadata($content->contentInfo, $cntMetaUpdateStruct);
        }
    }

    /**
     * @return eZRepository
     */
    protected function getEzRepository()
    {
        return $this->getContainer()->get('ezpublish.api.repository');
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentService
     */
    protected function getContentService()
    {
        return $this->getEzRepository()->getContentService();
    }

    /**
     * @return \eZ\Publish\API\Repository\SearchService
     */
    protected function getSearchService()
    {
        return $this->getEzRepository()->getSearchService();
    }
}