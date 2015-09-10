<?php
namespace Ayeo\Parser\Utils;

class Camelizer
{
    public function camelize($scored)
    {
        return lcfirst(implode('',array_map('ucfirst', array_map('strtolower', explode('_', $scored)))));
    }
}