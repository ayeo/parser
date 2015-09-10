<?php
namespace Ayeo\Parser\Test\Mock;

class ObjectWithPrivateCustomer
{
    private $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

}
