<?php

namespace Cx\Model\Proxies\__CG__\Cx\Modules\Shop\Model\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Order extends \Cx\Modules\Shop\Model\Entity\Order implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'id', 'customerId', 'currencyId', 'sum', 'dateTime', 'status', 'gender', 'company', 'firstname', 'lastname', 'address', 'city', 'zip', 'countryId', 'phone', 'vatAmount', 'shipmentAmount', 'shipmentId', 'paymentId', 'paymentAmount', 'ip', 'langId', 'note', 'modifiedOn', 'modifiedBy', 'billingGender', 'billingCompany', 'billingFirstname', 'billingLastname', 'billingAddress', 'billingCity', 'billingZip', 'billingCountryId', 'billingPhone', 'billingFax', 'billingEmail', 'lsvs', 'orderItems', 'relCustomerCoupons', 'lang', 'currency', 'shipper', 'payment', 'customer', 'validators', 'virtual');
        }

        return array('__isInitialized__', 'id', 'customerId', 'currencyId', 'sum', 'dateTime', 'status', 'gender', 'company', 'firstname', 'lastname', 'address', 'city', 'zip', 'countryId', 'phone', 'vatAmount', 'shipmentAmount', 'shipmentId', 'paymentId', 'paymentAmount', 'ip', 'langId', 'note', 'modifiedOn', 'modifiedBy', 'billingGender', 'billingCompany', 'billingFirstname', 'billingLastname', 'billingAddress', 'billingCity', 'billingZip', 'billingCountryId', 'billingPhone', 'billingFax', 'billingEmail', 'lsvs', 'orderItems', 'relCustomerCoupons', 'lang', 'currency', 'shipper', 'payment', 'customer', 'validators', 'virtual');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Order $proxy) {
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
    public function setCustomerId($customerId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCustomerId', array($customerId));

        return parent::setCustomerId($customerId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCustomerId', array());

        return parent::getCustomerId();
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrencyId($currencyId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrencyId', array($currencyId));

        return parent::setCurrencyId($currencyId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrencyId', array());

        return parent::getCurrencyId();
    }

    /**
     * {@inheritDoc}
     */
    public function setSum($sum)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSum', array($sum));

        return parent::setSum($sum);
    }

    /**
     * {@inheritDoc}
     */
    public function getSum()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSum', array());

        return parent::getSum();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTime($dateTime)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateTime', array($dateTime));

        return parent::setDateTime($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTime()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateTime', array());

        return parent::getDateTime();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', array($status));

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', array());

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function setGender($gender)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGender', array($gender));

        return parent::setGender($gender);
    }

    /**
     * {@inheritDoc}
     */
    public function getGender()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGender', array());

        return parent::getGender();
    }

    /**
     * {@inheritDoc}
     */
    public function setCompany($company)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCompany', array($company));

        return parent::setCompany($company);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompany()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCompany', array());

        return parent::getCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstname($firstname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFirstname', array($firstname));

        return parent::setFirstname($firstname);
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFirstname', array());

        return parent::getFirstname();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastname($lastname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastname', array($lastname));

        return parent::setLastname($lastname);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastname', array());

        return parent::getLastname();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddress', array($address));

        return parent::setAddress($address);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddress', array());

        return parent::getAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setCity($city)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCity', array($city));

        return parent::setCity($city);
    }

    /**
     * {@inheritDoc}
     */
    public function getCity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCity', array());

        return parent::getCity();
    }

    /**
     * {@inheritDoc}
     */
    public function setZip($zip)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setZip', array($zip));

        return parent::setZip($zip);
    }

    /**
     * {@inheritDoc}
     */
    public function getZip()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getZip', array());

        return parent::getZip();
    }

    /**
     * {@inheritDoc}
     */
    public function setCountryId($countryId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCountryId', array($countryId));

        return parent::setCountryId($countryId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountryId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCountryId', array());

        return parent::getCountryId();
    }

    /**
     * {@inheritDoc}
     */
    public function setPhone($phone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPhone', array($phone));

        return parent::setPhone($phone);
    }

    /**
     * {@inheritDoc}
     */
    public function getPhone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPhone', array());

        return parent::getPhone();
    }

    /**
     * {@inheritDoc}
     */
    public function setVatAmount($vatAmount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVatAmount', array($vatAmount));

        return parent::setVatAmount($vatAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getVatAmount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVatAmount', array());

        return parent::getVatAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function setShipmentAmount($shipmentAmount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setShipmentAmount', array($shipmentAmount));

        return parent::setShipmentAmount($shipmentAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getShipmentAmount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getShipmentAmount', array());

        return parent::getShipmentAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function setShipmentId($shipmentId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setShipmentId', array($shipmentId));

        return parent::setShipmentId($shipmentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getShipmentId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getShipmentId', array());

        return parent::getShipmentId();
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentId($paymentId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentId', array($paymentId));

        return parent::setPaymentId($paymentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentId', array());

        return parent::getPaymentId();
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentAmount($paymentAmount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentAmount', array($paymentAmount));

        return parent::setPaymentAmount($paymentAmount);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentAmount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentAmount', array());

        return parent::getPaymentAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function setIp($ip)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIp', array($ip));

        return parent::setIp($ip);
    }

    /**
     * {@inheritDoc}
     */
    public function getIp()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIp', array());

        return parent::getIp();
    }

    /**
     * {@inheritDoc}
     */
    public function setLangId($langId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLangId', array($langId));

        return parent::setLangId($langId);
    }

    /**
     * {@inheritDoc}
     */
    public function getLangId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLangId', array());

        return parent::getLangId();
    }

    /**
     * {@inheritDoc}
     */
    public function setNote($note)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNote', array($note));

        return parent::setNote($note);
    }

    /**
     * {@inheritDoc}
     */
    public function getNote()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNote', array());

        return parent::getNote();
    }

    /**
     * {@inheritDoc}
     */
    public function setModifiedOn($modifiedOn)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setModifiedOn', array($modifiedOn));

        return parent::setModifiedOn($modifiedOn);
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedOn()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModifiedOn', array());

        return parent::getModifiedOn();
    }

    /**
     * {@inheritDoc}
     */
    public function setModifiedBy($modifiedBy)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setModifiedBy', array($modifiedBy));

        return parent::setModifiedBy($modifiedBy);
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedBy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModifiedBy', array());

        return parent::getModifiedBy();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingGender($billingGender)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingGender', array($billingGender));

        return parent::setBillingGender($billingGender);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingGender()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingGender', array());

        return parent::getBillingGender();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingCompany($billingCompany)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingCompany', array($billingCompany));

        return parent::setBillingCompany($billingCompany);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingCompany()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingCompany', array());

        return parent::getBillingCompany();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingFirstname($billingFirstname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingFirstname', array($billingFirstname));

        return parent::setBillingFirstname($billingFirstname);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingFirstname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingFirstname', array());

        return parent::getBillingFirstname();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingLastname($billingLastname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingLastname', array($billingLastname));

        return parent::setBillingLastname($billingLastname);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingLastname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingLastname', array());

        return parent::getBillingLastname();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingAddress($billingAddress)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingAddress', array($billingAddress));

        return parent::setBillingAddress($billingAddress);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingAddress', array());

        return parent::getBillingAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingCity($billingCity)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingCity', array($billingCity));

        return parent::setBillingCity($billingCity);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingCity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingCity', array());

        return parent::getBillingCity();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingZip($billingZip)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingZip', array($billingZip));

        return parent::setBillingZip($billingZip);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingZip()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingZip', array());

        return parent::getBillingZip();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingCountryId($billingCountryId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingCountryId', array($billingCountryId));

        return parent::setBillingCountryId($billingCountryId);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingCountryId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingCountryId', array());

        return parent::getBillingCountryId();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingPhone($billingPhone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingPhone', array($billingPhone));

        return parent::setBillingPhone($billingPhone);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingPhone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingPhone', array());

        return parent::getBillingPhone();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingFax($billingFax)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingFax', array($billingFax));

        return parent::setBillingFax($billingFax);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingFax()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingFax', array());

        return parent::getBillingFax();
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingEmail($billingEmail)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBillingEmail', array($billingEmail));

        return parent::setBillingEmail($billingEmail);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillingEmail', array());

        return parent::getBillingEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function addLsv(\Cx\Modules\Shop\Model\Entity\Lsv $lsv)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addLsv', array($lsv));

        return parent::addLsv($lsv);
    }

    /**
     * {@inheritDoc}
     */
    public function removeLsv(\Cx\Modules\Shop\Model\Entity\Lsv $lsv)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeLsv', array($lsv));

        return parent::removeLsv($lsv);
    }

    /**
     * {@inheritDoc}
     */
    public function getLsvs()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLsvs', array());

        return parent::getLsvs();
    }

    /**
     * {@inheritDoc}
     */
    public function addOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItem $orderItem)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addOrderItem', array($orderItem));

        return parent::addOrderItem($orderItem);
    }

    /**
     * {@inheritDoc}
     */
    public function removeOrderItem(\Cx\Modules\Shop\Model\Entity\OrderItem $orderItem)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeOrderItem', array($orderItem));

        return parent::removeOrderItem($orderItem);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderItems()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrderItems', array());

        return parent::getOrderItems();
    }

    /**
     * {@inheritDoc}
     */
    public function addRelCustomerCoupon(\Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupon)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRelCustomerCoupon', array($relCustomerCoupon));

        return parent::addRelCustomerCoupon($relCustomerCoupon);
    }

    /**
     * {@inheritDoc}
     */
    public function removeRelCustomerCoupon(\Cx\Modules\Shop\Model\Entity\RelCustomerCoupon $relCustomerCoupon)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeRelCustomerCoupon', array($relCustomerCoupon));

        return parent::removeRelCustomerCoupon($relCustomerCoupon);
    }

    /**
     * {@inheritDoc}
     */
    public function getRelCustomerCoupons()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRelCustomerCoupons', array());

        return parent::getRelCustomerCoupons();
    }

    /**
     * {@inheritDoc}
     */
    public function setLang(\Cx\Core\Locale\Model\Entity\Locale $lang = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLang', array($lang));

        return parent::setLang($lang);
    }

    /**
     * {@inheritDoc}
     */
    public function getLang()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLang', array());

        return parent::getLang();
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrency(\Cx\Modules\Shop\Model\Entity\Currency $currency = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrency', array($currency));

        return parent::setCurrency($currency);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrency', array());

        return parent::getCurrency();
    }

    /**
     * {@inheritDoc}
     */
    public function setShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shipper = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setShipper', array($shipper));

        return parent::setShipper($shipper);
    }

    /**
     * {@inheritDoc}
     */
    public function getShipper()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getShipper', array());

        return parent::getShipper();
    }

    /**
     * {@inheritDoc}
     */
    public function setPayment(\Cx\Modules\Shop\Model\Entity\Payment $payment = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPayment', array($payment));

        return parent::setPayment($payment);
    }

    /**
     * {@inheritDoc}
     */
    public function getPayment()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPayment', array());

        return parent::getPayment();
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomer(\Cx\Core\User\Model\Entity\User $customer = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCustomer', array($customer));

        return parent::setCustomer($customer);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomer()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCustomer', array());

        return parent::getCustomer();
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
