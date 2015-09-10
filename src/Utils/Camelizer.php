<?php
namespace Ayeo\Parser\Utils;

class Camelizer
{
    function camelize($scored)
    {
        return lcfirst(implode('',array_map('ucfirst', array_map('strtolower', explode('_', $scored)))));
    }
}