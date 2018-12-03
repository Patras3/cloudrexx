<?php

namespace Cx\Model\Proxies\__CG__\Cx\Modules\Shop\Model\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Payment extends \Cx\Modules\Shop\Model\Entity\Payment implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * {@inheritDoc}
     * @param string $name
     */
    public function __get($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', array($name));

        return parent::__get($name);
    }





    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'locale', 'id', 'processorId', 'fee', 'freeFrom', 'ord', 'active', 'name', 'discountCoupons', 'orders', 'paymentProcessor', 'zones', 'validators', 'virtual');
        }

        return array('__isInitialized__', 'locale', 'id', 'processorId', 'fee', 'freeFrom', 'ord', 'active', 'name', 'discountCoupons', 'orders', 'paymentProcessor', 'zones', 'validators', 'virtual');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Payment $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function setTranslatableLocale($locale)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTranslatableLocale', array($locale));

        return parent::setTranslatableLocale($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setProcessorId($processorId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProcessorId', array($processorId));

        return parent::setProcessorId($processorId);
    }

    /**
     * {@inheritDoc}
     */
    public function getProcessorId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProcessorId', array());

        return parent::getProcessorId();
    }

    /**
     * {@inheritDoc}
     */
    public function setFee($fee)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFee', array($fee));

        return parent::setFee($fee);
    }

    /**
     * {@inheritDoc}
     */
    public function getFee()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFee', array());

        return parent::getFee();
    }

    /**
     * {@inheritDoc}
     */
    public function setFreeFrom($freeFrom)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFreeFrom', array($freeFrom));

        return parent::setFreeFrom($freeFrom);
    }

    /**
     * {@inheritDoc}
     */
    public function getFreeFrom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFreeFrom', array());

        return parent::getFreeFrom();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrd($ord)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrd', array($ord));

        return parent::setOrd($ord);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrd()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrd', array());

        return parent::getOrd();
    }

    /**
     * {@inheritDoc}
     */
    public function setActive($active)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setActive', array($active));

        return parent::setActive($active);
    }

    /**
     * {@inheritDoc}
     */
    public function getActive()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getActive', array());

        return parent::getActive();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function addDiscountCoupon(\Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupon)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addDiscountCoupon', array($discountCoupon));

        return parent::addDiscountCoupon($discountCoupon);
    }

    /**
     * {@inheritDoc}
     */
    public function removeDiscountCoupon(\Cx\Modules\Shop\Model\Entity\DiscountCoupon $discountCoupon)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeDiscountCoupon', array($discountCoupon));

        return parent::removeDiscountCoupon($discountCoupon);
    }

    /**
     * {@inheritDoc}
     */
    public function getDiscountCoupons()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDiscountCoupons', array());

        return parent::getDiscountCoupons();
    }

    /**
     * {@inheritDoc}
     */
    public function addOrder(\Cx\Modules\Shop\Model\Entity\Order $order)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addOrder', array($order));

        return parent::addOrder($order);
    }

    /**
     * {@inheritDoc}
     */
    public function removeOrder(\Cx\Modules\Shop\Model\Entity\Order $order)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeOrder', array($order));

        return parent::removeOrder($order);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrders()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrders', array());

        return parent::getOrders();
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentProcessor(\Cx\Modules\Shop\Model\Entity\PaymentProcessor $paymentProcessor = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentProcessor', array($paymentProcessor));

        return parent::setPaymentProcessor($paymentProcessor);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentProcessor()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentProcessor', array());

        return parent::getPaymentProcessor();
    }

    /**
     * {@inheritDoc}
     */
    public function addZone(\Cx\Modules\Shop\Model\Entity\Zone $zone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addZone', array($zone));

        return parent::addZone($zone);
    }

    /**
     * {@inheritDoc}
     */
    public function removeZone(\Cx\Modules\Shop\Model\Entity\Zone $zone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeZone', array($zone));

        return parent::removeZone($zone);
    }

    /**
     * {@inheritDoc}
     */
    public function getZones()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getZones', array());

        return parent::getZones();
    }

    /**
     * {@inheritDoc}
     */
    public function getComponentController()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getComponentController', array());

        return parent::getComponentController();
    }

    /**
     * {@inheritDoc}
     */
    public function setVirtual($virtual)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVirtual', array($virtual));

        return parent::setVirtual($virtual);
    }

    /**
     * {@inheritDoc}
     */
    public function isVirtual()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isVirtual', array());

        return parent::isVirtual();
    }

    /**
     * {@inheritDoc}
     */
    public function validate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'validate', array());

        return parent::validate();
    }

    /**
     * {@inheritDoc}
     */
    public function __call($methodName, $arguments)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__call', array($methodName, $arguments));

        return parent::__call($methodName, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', array());

        return parent::__toString();
    }

}
