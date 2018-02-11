<?php


namespace Flayfl\LaravelXSLT\Events;

use Illuminate\Queue\SerializesModels;
use Flayfl\LaravelXSLT\Engines\ExtendedDomDocument;

/**
 * Class XSLTEngineEvent
 * @package Flayfl\LaravelXSLT\Events
 */
class XSLTEngineEvent
{
    use SerializesModels;
    /**
     * @var ExtendedDomDocument
     */
    private $extendedDomDocument;
    /**
     * @var array
     */
    private $data;

    /**
     * XSLTEngineEvent constructor.
     * @param ExtendedDomDocument $extendedDomDocument
     * @param array $data
     */
    public function __construct(
        ExtendedDomDocument $extendedDomDocument,
        array $data
    ) {

        $this->extendedDomDocument = $extendedDomDocument;
        $this->data = $data;
    }

    /**
     * @return ExtendedDomDocument
     */
    public function getExtendedDomDocument() : ExtendedDomDocument
    {
        return $this->extendedDomDocument;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }
}
