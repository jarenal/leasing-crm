<?php
namespace App\ApiBundle\DBAL;

class TenancyType extends EnumType
{
    protected $name = 'tenancy_type';
    protected $values = array('Single', 'Joint', 'Shared');
}