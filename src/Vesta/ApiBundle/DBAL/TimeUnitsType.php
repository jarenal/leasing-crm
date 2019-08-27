<?php
namespace App\ApiBundle\DBAL;

class TimeUnitsType extends EnumType
{
    protected $name = 'time_units';
    protected $values = array('Days', 'Hours', 'Minutes');
}