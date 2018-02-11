<?php


namespace Flayfl\LaravelXSLT;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\ViewFinderInterface;
use Flayfl\LaravelXSLT\Engines\ExtendedDomDocument;
use Flayfl\LaravelXSLT\Exception\MethodNotFoundException;

/**
 * Class XSLTFactory
 * @package Flayfl\LaravelXSLT
 * @method ExtendedDomDocument addArrayToXmlByChild
 */
class XSLTFactory extends Factory
{
    /**
     * @var ExtendedDomDocument
     */
    private $extendedDomDocument;

    /**
     * @param EngineResolver $engines
     * @param ViewFinderInterface $finder
     * @param Dispatcher $events
     * @param ExtendedDomDocument $extendedDomDocument
     */
    public function __construct(
        EngineResolver $engines,
        ViewFinderInterface $finder,
        Dispatcher $events,
        ExtendedDomDocument $extendedDomDocument
    ) {
        parent::__construct($engines, $finder, $events);
        $this->extendedDomDocument = $extendedDomDocument;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (!method_exists($this->extendedDomDocument, $name)) {
            throw new MethodNotFoundException($name . ': Method Not Found');
        }

        return call_user_func_array([$this->extendedDomDocument, $name], $arguments);
    }
}
