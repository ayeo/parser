<?php
namespace Ayeo\Parser;

use Ayeo\Parser\Utils\Camelizer;

class Parser
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $pattern = '#\{\{(.+?)\}\}#';

    private $camelizer;

    private $prefix = '';

    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function parse($template, array $data)
    {
        $this->data = $data;
        $this->camelizer = new Camelizer();
        return preg_replace_callback($this->pattern, [$this, 'callback'], $template);
    }

    private function callback(array $matches)
    {
        $places = explode('.', $this->prefix.$matches[1]);

        $c = $this->data;
        $processedPath = [];
        foreach ($places as $place)
        {
            $processedPath[] = $place;

            if (is_object($c))
            {
                $reflection = new \ReflectionClass(get_class($c));
                $methodName = 'get'.$this->camelizer->camelize($place);

                try
                {
                    $property = $reflection->getProperty($place);
                }
                catch (\Exception $e)
                {
                    $property = null;
                }


                if ($property && $property->isPublic())
                {
                    $c = $c->{$place};
                }
                else if ($reflection->hasMethod($methodName))
                {
                    $c = call_user_func(array($c, $methodName));
                }
                else
                {
                    $message = "Class %s has not property or getter for: %s. Full path: %s";
                    throw new \Exception(sprintf($message, get_class($c), $place, join('.', $places)));
                }
            }
            else if (is_array($c))
            {
                $c = $c[$place];
            }

            if (is_null($c))
            {
                throw new \Exception('Null value for: '.join('.', $processedPath));
            }
        }

        return $c;
    }
}