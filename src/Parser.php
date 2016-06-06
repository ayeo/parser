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
    private $prefix;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var Camelizer
     */
    private $camelizer;

    /**
     * @param string $prefix
     * @param string $open
     * @param string $close
     */
    public function __construct($prefix = '', $open = "{{", $close = "}}")
    {
        $this->prefix = $prefix;
        $this->setEmbraceStrings($open, $close);
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function parse($template, array $data)
    {
        $this->data = $data;
        $this->camelizer = new Camelizer();
        return preg_replace_callback($this->pattern, [$this, 'callback'], $template);
    }

    /**
     * @param string $open
     * @param string $close
     */
    public function setEmbraceStrings($open, $close)
    {
        if (preg_match("@#@", $open))
        {
            throw new \LogicException("Open delimiter must not contain # char");
        }

        if (preg_match("@#@", $close))
        {
            throw new \LogicException("Close delimiter must not contain # char");
        }

        $this->pattern = sprintf("#%s(.+?)%s#", preg_quote($open), preg_quote($close));
    }

    /**
     * @param array $matches
     * @return array|mixed
     * @throws \Exception
     */
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
