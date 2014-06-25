<?php
/*
 * Copyright (c) 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of Formal BEAR.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\FormalBEAR\Module;

use BEAR\Package\Dev\Module\ExceptionHandle\ExceptionHandleModule;
use BEAR\Package\Module\Cache\CacheAspectModule;
use BEAR\Package\Module\Di\DiCompilerModule;
use BEAR\Package\Module\Di\DiModule;
use BEAR\Package\Module\Resource\DevResourceModule;
use BEAR\Package\Module\Resource\NullCacheModule;
use BEAR\Package\Module\Resource\SignalParamModule;
use BEAR\Package\Provide\ApplicationLogger\ApplicationLoggerModule;
use BEAR\Package\Provide\ApplicationLogger\DevApplicationLoggerModule;
use BEAR\Package\Provide\ConsoleOutput\ConsoleOutputModule;
use BEAR\Package\Provide\ResourceView\HalModule;
use BEAR\Package\Provide\ResourceView\TemplateEngineRendererModule;
use BEAR\Package\Provide\Router\WebRouterModule;
use BEAR\Package\Provide\WebResponse\HttpFoundationModule;
use BEAR\Resource\Module\EmbedResourceModule;
use BEAR\Resource\Module\NamedArgsModule;
use BEAR\Sunday\Module\Cache\CacheModule;
use BEAR\Sunday\Module\Code\CachedAnnotationModule;
use BEAR\Sunday\Module\Constant\NamedModule;
use Ray\Di\AbstractModule;
use Ray\Di\Di\Scope;

use PHPMentors\FormalBEAR\Config\FrameworkConfiguration;

class FrameworkModule extends TransformationModule
{
    /**
     * @var string
     */
    private $appDir;

    /**
     * @var string
     */
    private $context;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param \Ray\Di\AbstractModule $module
     * @param string                 $namespace
     * @param string                 $context
     * @param string                 $appDir
     */
    public function __construct(AbstractModule $module, $namespace, $context, $appDir)
    {
        parent::__construct($module);

        $this->namespace = $namespace;
        $this->context = $context;
        $this->appDir = $appDir;
    }

    /**
     * {@inheritDoc}
     */
    protected function createConfiguration()
    {
        return new FrameworkConfiguration($this->namespace, $this->appDir);
    }

    /**
     * {@inheritDoc}
     */
    protected function transform(array $config)
    {
        // Sunday Module
        $this->install(new NamedModule($this->createNamedConstants($config)));
        $this->install(new CacheModule($this));
        $this->install(new CachedAnnotationModule($this));

        // Package Module
        $this->install(new CacheAspectModule($this));
        $this->install(new DiCompilerModule($this));
        $this->install(new DiModule($this));
        $this->install(new ExceptionHandleModule($this));
        $this->install(new NamedArgsModule($this));
        $this->install(new ResourceClientModule($config['app_name'], $config['resource_dir']));
        $this->install(new SignalParamModule($this, $config['signal_parameters']));

        // Resource Module
        $this->install(new EmbedResourceModule($this));

        // Provide module (BEAR.Sunday extension interfaces)
        $this->install(new HttpFoundationModule($this));
        $this->install(new ConsoleOutputModule($this));
        $this->install(new WebRouterModule($this));
        $this->install(new TemplateEngineRendererModule($this));

        // Contextual Binding
        if ($this->context == 'api') {
            $this->install(new HalModule($this));
        } elseif ($this->context == 'prod') {
            $this->install(new CacheAspectModule($this));
        } elseif ($this->context == 'dev') {
            $this->install(new ApplicationLoggerModule($this));
            $this->install(new DevResourceModule($this));
            $this->install(new DevApplicationLoggerModule($this));
        } elseif ($this->context == 'test') {
            $this->install(new NullCacheModule($this));
        }

        $this->bind('BEAR\Sunday\Extension\Application\AppInterface')->to($config['app_class'])->in(Scope::SINGLETON);
    }

    /**
     * @param  array $config
     * @return array
     */
    private function createNamedConstants(array $config)
    {
        return [
            'package_dir' => dirname(dirname(dirname(dirname((new \ReflectionClass('BEAR\Package\Module\Package\PackageModule'))->getFileName())))),
            'app_context' => $this->context,
        ] + $config['named_constants'];
    }
}
