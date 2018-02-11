<?php


namespace Flayfl\LaravelXSLT\Engines;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Engine;
use Flayfl\LaravelXSLT\Events\XSLTEngineEvent;
use XsltProcessor;

/**
 * Class XSLTEngine
 * @package Flayfl\LaravelXSLT\Engines
 */
class XSLTEngine implements Engine
{
    const EVENT_NAME = XSLTEngineEvent::class;

    /**
     * @var XsltProcessor
     */
    protected $xsltProcessor;
    /**
     * @var ExtendedDomDocument
     */
    protected $extendedDomDocument;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * XSLTEngine constructor.
     * @param XsltProcessor $xsltProcessor
     * @param ExtendedDomDocument $extendedDomDocument
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        XsltProcessor $xsltProcessor,
        ExtendedDomDocument $extendedDomDocument,
        Dispatcher $dispatcher
    ) {
        $this->extendedDomDocument = $extendedDomDocument;
        $this->xsltProcessor = $xsltProcessor;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $path
     * @param array $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $this->dispatcher->dispatch(self::EVENT_NAME, new XSLTEngineEvent($this->extendedDomDocument, $data));

        $this->xsltProcessor->importStylesheet(simplexml_load_file($path));

        return $this->xsltProcessor->transformToXml($this->extendedDomDocument->getDoc());
    }
}
