<?php
/**
 * Created by PhpStorm.
 * User: moritz
 * Date: 31.08.14
 * Time: 15:41
 */

namespace Kuborgh\Bundle\MeasureBundle\Command;


use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Repository as eZRepository;
use eZ\Publish\API\Repository\Values\Content\Query;

class CreateTranslationsCommand extends ContainerAwareCommand {

    const ARGUMENT_CONTENT_TYPE = "type";
    const ARGUMNET_LANGUAGE_CODE = "lang";

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('kb:measure:translate');
        $this->setDescription('Create a translation for each content object of the given type in teh given language code');
        $this->addArgument(self::ARGUMENT_CONTENT_TYPE, InputArgument::REQUIRED, 'eZ Content Type');
        $this->addArgument(self::ARGUMNET_LANGUAGE_CODE, InputArgument::REQUIRED, 'Language Code');
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
        $type = $input->getArgument(self::ARGUMENT_CONTENT_TYPE);
        $langCode = $input->getArgument(self::ARGUMNET_LANGUAGE_CODE);

        // login
        $this->getEzRepository()->setCurrentUser($this->getEzRepository()->getUserService()->loadUserByLogin('admin'));

        // load language id
        $output->writeln('Load language....');
        $this->getEzRepository()->getContentLanguageService()->loadLanguage($langCode);

        // work
        $contents = $this->loadContentObjects($type);
        $output->writeln('Loading....');
        $cntSrvc = $this->getContentService();
        $output->writeln('Creating....');
        foreach($contents as $content) {
            $cntDraft = $cntSrvc->createContentDraft($content->contentInfo);

            $contentUpdateStruct = $cntSrvc->newContentUpdateStruct();
            $contentUpdateStruct->initialLanguageCode = $langCode;
            foreach($content->getFields() as $field) {
                $contentUpdateStruct->setField($field->fieldDefIdentifier, $field->value);
            }
            $contentDraft = $cntSrvc->updateContent( $cntDraft->versionInfo, $contentUpdateStruct );

            $content = $cntSrvc->publishVersion($contentDraft->versionInfo);

            // Update publish date
            $cntMetaUpdateStruct = $cntSrvc->newContentMetadataUpdateStruct();
            $cntMetaUpdateStruct->publishedDate = new \DateTime();
            $cntMetaUpdateStruct->mainLanguageCode = $langCode;
            $cntSrvc->updateContentMetadata($content->contentInfo, $cntMetaUpdateStruct);

            $output->write('.');
        }

        $output->writeln('Done');
    }

    /**
     * Load all content objects matching teh given identification ( name )
     *
     * @param string $contentTypeName
     *
     * @return Content[]
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