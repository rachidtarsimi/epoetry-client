<?php

namespace Notification;

use GuzzleHttp\Psr7\Request;
use Monolog\Logger;
use OpenEuropa\EPoetry\Notification\Event as Notification;
use OpenEuropa\EPoetry\Notification\Exception\NotificationException;
use OpenEuropa\EPoetry\Notification\Type\Product;
use OpenEuropa\EPoetry\Notification\Type\ProductReference;
use OpenEuropa\EPoetry\Notification\Type\RequestReference;
use OpenEuropa\EPoetry\NotificationServerFactory;
use OpenEuropa\EPoetry\Serializer\Serializer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Test SOAP notification handler.
 */
class NotificationHandlerTest extends TestCase
{

    protected Serializer $serializer;

    protected LoggerInterface $logger;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = new Serializer();
        $this->logger = new Logger('test');
    }

    /**
     * Test all status changes notification events.
     *
     * @runInSeparateProcess
     * @dataProvider productStatusChangeEventsDataProvider
     */
    public function testProductStatusChangeEvents(string $class, string $status, string $message)
    {
        // Encapsulate assertions in an event subscriber.
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->getSubscriber(function (Event $event) use ($class, $status) {
            $this->assertInstanceOf($class, $event);
            $this->assertInstanceOf(Product::class, $event->getProduct());
            $this->assertEquals($status, $event->getProduct()->getStatus());
            $this->assertEquals(false, $event->getProduct()->hasFile());
            $this->assertEquals(false, $event->getProduct()->hasFormat());
            $this->assertEquals(false, $event->getProduct()->hasName());
            $this->assertInstanceOf(ProductReference::class, $event->getProduct()->getProductReference());
            $productReference = $event->getProduct()->getProductReference();
            $this->assertEquals('SK', $productReference->getLanguage());
            $this->assertInstanceOf(RequestReference::class, $productReference->getRequestReference());
            $this->assertEquals('AGRI-2022-93-(0)-0-TRA', $productReference->getRequestReference()->getReference());
            $event->setSuccessResponse('Success message.');
        }));

        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequestByXml($message);
        $response = $server->handle($request);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(<<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://eu.europa.ec.dgt.epoetry"><SOAP-ENV:Body><ns1:receiveNotificationResponse><return><success>true</success><message>Success message.</message></return></ns1:receiveNotificationResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>
RESPONSE, trim($response->getBody()->getContents()));
    }

    /**
     * Test data provider.
     *
     * This covers all notification that do not have date-related information,
     * such as "Ongoing", nor deliver the actual product. For those two we have
     * separate tests.
     *
     * @return array
     */
    public function productStatusChangeEventsDataProvider(): array
    {
        $data = [];
        foreach ([
            'Accepted' => Notification\Product\StatusChangeAcceptedEvent::class,
            'Cancelled' => Notification\Product\StatusChangeCancelledEvent::class,
            'Closed' => Notification\Product\StatusChangeClosedEvent::class,
            'ReadyToBeSent' => Notification\Product\StatusChangeReadyToBeSentEvent::class,
            'Requested' => Notification\Product\StatusChangeRequestedEvent::class,
            'Sent' => Notification\Product\StatusChangeSentEvent::class,
            'Suspended' => Notification\Product\StatusChangeSuspendedEvent::class,
        ] as $status => $class) {
            $data[] = [
                'class' => $class,
                'status' => $status,
                'message' => sprintf(<<<MESSAGE
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:eu="http://eu.europa.ec.dgt.epoetry">
    <soapenv:Header/>
    <S:Body xmlns:S="http://schemas.xmlsoap.org/soap/envelope/">
        <ns0:receiveNotification xmlns:ns0="http://eu.europa.ec.dgt.epoetry">
            <notification>
                <notificationType>ProductStatusChange</notificationType>
                <product>
                    <productReference>
                        <requestReference>
                            <requesterCode>AGRI</requesterCode>
                            <year>2022</year>
                            <number>93</number>
                            <part>0</part>
                            <version>0</version>
                            <productType>TRA</productType>
                        </requestReference>
                        <language>SK</language>
                    </productReference>
                    <status>%s</status>
                </product>
            </notification>
        </ns0:receiveNotification>
    </S:Body>
</soapenv:Envelope>
MESSAGE, $status),
            ];
        }

        return $data;
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatusChangeOngoingEvent()
    {
        // Encapsulate assertions in an event subscriber.
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->getSubscriber(function (Event $event) {
            $this->assertInstanceOf(Notification\Product\StatusChangeOngoingEvent::class, $event);
            $this->assertInstanceOf(Product::class, $event->getProduct());
            $this->assertInstanceOf(\DateTimeInterface::class, $event->getAcceptedDeadline());
            $this->assertEquals('Mon, 04 Apr 22 10:51:00 +0000', $event->getAcceptedDeadline()->format(\DATE_RFC822));
            $this->assertEquals('Ongoing', $event->getProduct()->getStatus());
            $this->assertEquals(false, $event->getProduct()->hasFile());
            $this->assertEquals(false, $event->getProduct()->hasFormat());
            $this->assertEquals(false, $event->getProduct()->hasName());
            $this->assertInstanceOf(ProductReference::class, $event->getProduct()->getProductReference());
            $productReference = $event->getProduct()->getProductReference();
            $this->assertEquals('CS', $productReference->getLanguage());
            $this->assertInstanceOf(RequestReference::class, $productReference->getRequestReference());
            $this->assertEquals('AGRI-2022-81-(1)-0-TRA', $productReference->getRequestReference()->getReference());
            $event->setSuccessResponse('Success message.');
        }));

        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequest('productStatusChangeOngoing.xml');
        $response = $server->handle($request);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(<<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://eu.europa.ec.dgt.epoetry"><SOAP-ENV:Body><ns1:receiveNotificationResponse><return><success>true</success><message>Success message.</message></return></ns1:receiveNotificationResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>
RESPONSE, trim($response->getBody()->getContents()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDeliveryEvent()
    {
        // Encapsulate assertions in an event subscriber.
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->getSubscriber(function (Event $event) {
            $this->assertInstanceOf(Notification\Product\DeliveryEvent::class, $event);
            $this->assertInstanceOf(Product::class, $event->getProduct());
            $this->assertEquals('Sent', $event->getProduct()->getStatus());
            $this->assertEquals(true, $event->getProduct()->hasFile());
            $this->assertEquals(true, $event->getProduct()->hasFormat());
            $this->assertEquals(true, $event->getProduct()->hasName());
            $this->assertInstanceOf(ProductReference::class, $event->getProduct()->getProductReference());
            $productReference = $event->getProduct()->getProductReference();
            $this->assertEquals('FR', $productReference->getLanguage());
            $this->assertInstanceOf(RequestReference::class, $productReference->getRequestReference());
            $this->assertEquals('SG-2022-343-(1)-0-TRA', $productReference->getRequestReference()->getReference());
            $this->assertEquals('File content.', $event->getDeliveryContent());
            $event->setSuccessResponse('Success message.');
        }));

        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequest('productDeliverySent.xml');
        $response = $server->handle($request);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(<<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://eu.europa.ec.dgt.epoetry"><SOAP-ENV:Body><ns1:receiveNotificationResponse><return><success>true</success><message>Success message.</message></return></ns1:receiveNotificationResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>
RESPONSE, trim($response->getBody()->getContents()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testChangeAcceptedEvent()
    {
        // Encapsulate assertions in an event subscriber.
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->getSubscriber(function (Event $event) {
            $this->assertInstanceOf(Notification\Request\StatusChangeAcceptedEvent::class, $event);
            $this->assertEquals('DGT.S.S-1.P-1', $event->getPlanningSector());
            $this->assertEquals('teodomi', $event->getPlanningAgent());
            $this->assertEquals('Accepted', $event->getLinguisticRequest()->getStatus());
            $this->assertEquals('SG-2022-127-(0)-0-TRA', $event->getLinguisticRequest()->getRequestReference()->getReference());
            $event->setSuccessResponse('Success message.');
        }));

        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequest('requestStatusChangeAccepted.xml');
        $response = $server->handle($request);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(<<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://eu.europa.ec.dgt.epoetry"><SOAP-ENV:Body><ns1:receiveNotificationResponse><return><success>true</success><message>Success message.</message></return></ns1:receiveNotificationResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>
RESPONSE, trim($response->getBody()->getContents()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testChangeRejectedEvent()
    {
        // Encapsulate assertions in an event subscriber.
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($this->getSubscriber(function (Event $event) {
            $this->assertInstanceOf(Notification\Request\StatusChangeRejectedEvent::class, $event);
            $this->assertEquals('DGT.S.S-1.P-2', $event->getPlanningSector());
            $this->assertEquals('collafc', $event->getPlanningAgent());
            $this->assertEquals('Rejected', $event->getLinguisticRequest()->getStatus());
            $this->assertEquals('AGRI-2022-83-(0)-0-TRA', $event->getLinguisticRequest()->getRequestReference()->getReference());
            $event->setSuccessResponse('Success message.');
        }));

        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequest('requestStatusChangeRejected.xml');
        $response = $server->handle($request);

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertEquals(<<<RESPONSE
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://eu.europa.ec.dgt.epoetry"><SOAP-ENV:Body><ns1:receiveNotificationResponse><return><success>true</success><message>Success message.</message></return></ns1:receiveNotificationResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>
RESPONSE, trim($response->getBody()->getContents()));
    }

    /**
     * @runInSeparateProcess
     */
    public function testNotificationHandlerError(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage("The ePoetry notification event 'RequestStatusChange' was not handled correctly");

        $eventDispatcher = new EventDispatcher();
        $server = new NotificationServerFactory('', $eventDispatcher, $this->logger, $this->serializer);
        $request = $this->getNotificationRequest('requestStatusChangeRejected.xml');
        $server->handle($request);
    }

    /**
     * Get a HTTP request object having given fixture as body.
     *
     * @param string $fixtureName
     *   Fixture filename.
     *
     * @return \Psr\Http\Message\RequestInterface
     *   HTTP request object.
     */
    private function getNotificationRequest(string $fixtureName): RequestInterface
    {
        $xml = file_get_contents(__DIR__ . '/fixtures/' . $fixtureName);
        return $this->getNotificationRequestByXml($xml);
    }

    /**
     * Get a HTTP request object having given XML as body.
     *
     * @param string $xml
     *   String containing actual request XML.
     *
     * @return \Psr\Http\Message\RequestInterface
     *   HTTP request object.
     */
    private function getNotificationRequestByXml(string $xml): RequestInterface
    {
        return new Request('POST', 'http://foo', [
            'accept' => 'text/xml',
            'content-type' => 'text/xml; charset=utf-8',
            'SOAPAction' => 'http://eu.europa.ec.dgt.epoetry/DgtClientNotificationReceiverWS/receiveNotificationRequest',
        ], $xml);
    }

    /**
     * Build and get subscriber.
     *
     * @param callable $assert
     *
     * @return \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    private function getSubscriber(callable $assert): EventSubscriberInterface
    {
        return new class($assert) implements EventSubscriberInterface {

            private $assert;

            /**
             * @param callable $assert
             */
            public function __construct(callable $assert)
            {
                $this->assert = $assert;
            }

            /**
             * @inheritDoc
             */
            public static function getSubscribedEvents()
            {
                return [
                    // Product notifications.
                    Notification\Product\StatusChangeAcceptedEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeCancelledEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeClosedEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeOngoingEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeReadyToBeSentEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeRequestedEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeSentEvent::NAME => 'doAssert',
                    Notification\Product\StatusChangeSuspendedEvent::NAME => 'doAssert',
                    Notification\Product\DeliveryEvent::NAME => 'doAssert',
                    // Request notifications.
                    Notification\Request\StatusChangeAcceptedEvent::NAME => 'doAssert',
                    Notification\Request\StatusChangeCancelledEvent::NAME => 'doAssert',
                    Notification\Request\StatusChangeExecutedEvent::NAME => 'doAssert',
                    Notification\Request\StatusChangeRejectedEvent::NAME => 'doAssert',
                    Notification\Request\StatusChangeSuspendedEvent::NAME => 'doAssert',
                ];
            }

            /**
             * @param \Symfony\Contracts\EventDispatcher\Event $event
             *
             * @return void
             */
            public function doAssert(Event $event): void
            {
                ($this->assert)($event);
            }
        };
    }
}
