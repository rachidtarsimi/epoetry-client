<?php

namespace OpenEuropa\EPoetry\Notification\Event\Product;

use OpenEuropa\EPoetry\Notification\Type\Product;

/**
 * Event fired when the status of the product changes to "ongoing".
 */
class StatusChangeOngoingEvent extends BaseEvent {

    public const NAME = 'epoetry.notification.product_status_change_ongoing';

    private \DateTime $acceptedDeadline;

    /**
     * @param \OpenEuropa\EPoetry\Notification\Type\Product $product
     * @param \DateTime $acceptedDeadline
     */
    public function __construct(Product $product, \DateTimeInterface $acceptedDeadline) {
        parent::__construct($product);
        $this->acceptedDeadline = $acceptedDeadline;
    }

    /**
     * @return \DateTime
     */
    public function getAcceptedDeadline(): \DateTime {
        return $this->acceptedDeadline;
    }

}
