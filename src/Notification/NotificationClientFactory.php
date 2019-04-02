<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Notification;

use OpenEuropa\EPoetry\ClientFactory;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;

/**
 * Class NotificationClientFactory.
 */
class NotificationClientFactory extends ClientFactory
{
    /**
     * Name of client class.
     *
     * @var string
     */
    protected $clientName = NotificationClient::class;

    /**
     * Build the WSDL with file on resources.
     *
     * @param string $endpoint
     *    Endpoint url
     *
     * @return string
     */
    protected function buildWsdl(string $endpoint): string
    {
        $wsdl = file_get_contents(__DIR__ . '/../../resources/NotificationServiceWSDL.xml');
        $wsdl = str_replace('%ENDPOINT%', $endpoint, $wsdl);

        $xsd = file_get_contents(__DIR__ . '/../../resources/NotificationServiceXSD.xml');
        $wsdl = str_replace('NotificationServiceXSD.xml', 'plain;base64,' . base64_encode($xsd), $wsdl);

        return 'data://text/plain;base64,' . base64_encode($wsdl);
    }

    /**
     * Return client mapping.
     *
     * @return ClassMapCollection
     */
    protected function getClassMapCollection()
    {
        return NotificationClassmap::getCollection();
    }
}
