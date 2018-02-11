<?php


namespace Flayfl\LaravelXSLT;

use Illuminate\Support\ServiceProvider;
use Flayfl\LaravelXSLT\Engines\ExtendedDomDocument;
use Flayfl\LaravelXSLT\Engines\XSLTEngine;
use XSLTProcessor;

/**
 * Class XSLTServiceProvider
 * @package Flayfl\LaravelXSLT
 */
class XSLTServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            $xsltProcessor = new XsltProcessor();
            $xsltProcessor->registerPHPFunctions();
            $extendedDomDocument = new ExtendedDomDocument();

            $factory = new XSLTFactory(
                $app['view.engine.resolver'],
                $app['view.finder'],
                $app['events'],
                $extendedDomDocument
            );
            $factory->setContainer($app);
            $factory->addExtension(
                'xsl',
                'xslt',
                function () use ($xsltProcessor, $extendedDomDocument, $app) {
                    return new XSLTEngine($xsltProcessor, $extendedDomDocument, $app['events']);
                }
            );

            return $factory;
        });
    }
}