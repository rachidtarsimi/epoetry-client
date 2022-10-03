<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Console\Command;

use OpenEuropa\EPoetry\Request\Type\AuxiliaryDocumentsIn;
use OpenEuropa\EPoetry\Request\Type\ContactPersonIn;
use OpenEuropa\EPoetry\Request\Type\Contacts;
use OpenEuropa\EPoetry\Request\Type\CreateLinguisticRequest;
use OpenEuropa\EPoetry\Request\Type\DocumentIn;
use OpenEuropa\EPoetry\Request\Type\LinguisticSectionOut;
use OpenEuropa\EPoetry\Request\Type\LinguisticSections;
use OpenEuropa\EPoetry\Request\Type\ModifyProductRequestIn;
use OpenEuropa\EPoetry\Request\Type\OriginalDocumentIn;
use OpenEuropa\EPoetry\Request\Type\Products;
use OpenEuropa\EPoetry\Request\Type\ReferenceDocuments;
use OpenEuropa\EPoetry\Request\Type\RequestDetailsIn;
use OpenEuropa\EPoetry\Request\Type\SrcDocumentIn;
use OpenEuropa\EPoetry\RequestClientFactory;
use OpenEuropa\EPoetry\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RequestCommand extends Command
{
    protected static $defaultName = 'request';

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    public function __construct(LoggerInterface $logger, SerializerInterface $serializer)
    {
        parent::__construct(null);
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Build and send a CreateRequests to ePoetry.')
            ->addArgument('endpoint', InputArgument::REQUIRED, 'ePoetry service endpoint')
            ->addArgument('ticket', InputArgument::REQUIRED, 'Authentication ticket')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factory = new RequestClientFactory($input->getArgument('endpoint'), $input->getArgument('ticket'), null, $this->logger);
        $this->logger->info('Endpoint: ' . $factory->getEndpoint());
        $this->logger->info('Proxy ticket: ' . $factory->getProxyTicket());
        $client = $factory->getRequestClient();

        $response = $client->createLinguisticRequest($this->getCreateLinguisticRequest());
        $this->logger->info($this->serializer->toString($response, 'json'));
        return 0;
    }

    /**
     * Gets test CreateLinguisticRequest object.
     */
    protected function getCreateLinguisticRequest(): CreateLinguisticRequest
    {
        $document = new DocumentIn();
        $document->setFileName('test.docx')
            ->setLanguage('EN')
            ->setComment('test')
            ->setContent('cid:303605824112');

        $referenceDocuments = new ReferenceDocuments();
        $referenceDocuments->addDocument($document);

        $srcDocument = new SrcDocumentIn();
        $srcDocument->setFileName('test2222SRC.docx')
            ->setComment('777888877')
            ->setContent('cid:1531884704226');

        $auxiliaryDocuments = new AuxiliaryDocumentsIn();
        $auxiliaryDocuments->setReferenceDocuments($referenceDocuments)
            ->setSrcDocument($srcDocument);

        $requestDetails = new RequestDetailsIn();
        $requestDetails->setTitle('Request title')
            ->setRequestedDeadline(\DateTime::createFromFormat(DATE_RFC3339, '2029-07-01T11:51:00+01:00'))
            ->setSensitive(false)
            ->setDestination('PUBLIC')
            ->setProcedure('DEGHP')
            ->setSlaAnnex('ANNEX8A')
            ->setSlaCommitment('2225555')
            ->setComment('comment')
            ->setAccessibleTo('CONTACTS')
            ->setKeyword1('keyword1')
            ->setKeyword2('keyword2')
            ->setKeyword3('keyword3')
            ->setAuxiliaryDocuments($auxiliaryDocuments);
        $contacts = (new Contacts())
            ->addContact(new ContactPersonIn('liekejo', 'REQUESTER'))
            ->addContact(new ContactPersonIn('liekejo', 'AUTHOR'))
            ->addContact(new ContactPersonIn('liekejo', 'RECIPIENT'));
        $requestDetails->setContacts($contacts);

        $linguisticSections = (new LinguisticSections())
            ->addLinguisticSection(new LinguisticSectionOut('FR'));
        $originalDocument = (new OriginalDocumentIn())
            ->setTrackChanges(false)
            ->setFileName('TEST_FILE_ORIGINALP.docx')
            ->setContent('cid:267736828531')
            ->setLinguisticSections($linguisticSections)
            ->setComment('');
        $requestDetails->setOriginalDocument($originalDocument);

        $productRequestIn = (new ModifyProductRequestIn())
            ->setLanguage('IT')
            ->setRequestedDeadline(\DateTime::createFromFormat(DATE_RFC3339, '2029-07-06T11:51:00+01:00'))
            ->setTrackChanges(false);
        $products = (new Products())
            ->addProduct($productRequestIn);
        $requestDetails->setProducts($products);

        return (new CreateLinguisticRequest())
            ->setRequestDetails($requestDetails)
            ->setApplicationName('appname')
            ->setTemplateName('DEFAULT');
    }
}
