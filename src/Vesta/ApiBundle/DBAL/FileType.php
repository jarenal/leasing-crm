<?php
namespace App\ApiBundle\DBAL;

class FileType extends EnumType
{
    protected $name = 'file_type';
    protected $values = array('D', 'I');
}