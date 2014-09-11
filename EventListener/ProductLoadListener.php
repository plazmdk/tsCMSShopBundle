<?php
namespace tsCMS\ShopBundle\EventListener;



use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use tsCMS\ShopBundle\Entity\Product;

class ProductLoadListener {
    /** @var  ContainerInterface */
    protected $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof Product) {
            // Calculate the actual buyprice for a single item
            $buyPrice = $entity->getPrice();

            $entity->setPrice($buyPrice);
        }
    }
}