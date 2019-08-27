<?php
namespace App\ApiBundle\DBAL;

class ReviewTimescaleType extends EnumType
{
    protected $name = 'review_timescale';
    protected $values = array('Weeks', 'Months', 'Years');
}