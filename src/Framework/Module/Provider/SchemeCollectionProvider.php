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

/*
 * Copyright (c) 2013-2014 Akihito Koriyama <akihito.koriyama@gmail.com>,
 * All rights reserved.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 3-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-3-Clause
 */

namespace PHPMentors\FormalBEAR\Framework\Module\Provider;

use BEAR\Resource\Adapter\Http;
use BEAR\Resource\Exception\AppName;
use BEAR\Resource\SchemeCollection;
use Ray\Di\ProviderInterface;
use Ray\Di\InstanceInterface;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use PHPMentors\FormalBEAR\Framework\Resource\AppAdapter;

/**
 * SchemeCollection provider
 */
class SchemeCollectionProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $appName;

    /**
     * @var string
     */
    protected $resourceDir;

    /**
     * @var \Ray\Di\InstanceInterface
     */
    protected $injector;

    /**
     * @param string $appName
     *
     * @return void
     *
     * @throws \BEAR\Resource\Exception\AppName
     * @Inject
     * @Named("appName=app_name,resourceDir=resource_dir")
     */
    public function setAppName($appName, $resourceDir)
    {
        if (!is_string($appName)) {
            throw new AppName($appName);
        }

        $this->appName = $appName;
        $this->resourceDir = $resourceDir;
    }

    /**
     * @param \Ray\Di\InstanceInterface $injector
     *
     * @Inject
     */
    public function setInjector(InstanceInterface $injector)
    {
        $this->injector = $injector;
    }

    /**
     * Return instance
     *
     * @return \BEAR\Resource\SchemeCollection
     */
    public function get()
    {
        $schemeCollection = new SchemeCollection();
        $pageAdapter = new AppAdapter($this->injector, $this->appName, 'Resource\Page', $this->resourceDir . '/Page');
        $appAdapter = new AppAdapter($this->injector, $this->appName, 'Resource\App', $this->resourceDir . '/App');
        $schemeCollection->scheme('page')->host('self')->toAdapter($pageAdapter);
        $schemeCollection->scheme('app')->host('self')->toAdapter($appAdapter);
        $schemeCollection->scheme('http')->host('*')->toAdapter(new Http());

        return $schemeCollection;
    }
}
