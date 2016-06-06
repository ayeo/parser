<?php
namespace Ayeo\Parser\Utils;

class Camelizer
{
    /**
     * @param string $scored
     * @return string
     */
    public function camelize($scored)
    {
        return lcfirst(implode('',array_map('ucfirst', array_map('strtolower', explode('_', $scored)))));
    }
}