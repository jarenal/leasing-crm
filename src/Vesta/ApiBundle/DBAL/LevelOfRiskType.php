<?php
namespace App\ApiBundle\DBAL;

class LevelOfRiskType extends EnumType
{
    protected $name = 'level_of_risk';
    protected $values = array('Low', 'Medium', 'High', 'N/A');
}