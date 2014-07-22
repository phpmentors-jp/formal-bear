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
 * Copyright (c) 2011-2014 Akihito Koriyama <akihito.koriyama@gmail.com>,
 * All rights reserved.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 3-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-3-Clause
 */

namespace PHPMentors\FormalBEAR\Framework\Resource;

use BEAR\Resource\Adapter\AdapterInterface;
use BEAR\Resource\Adapter\Iterator\AppIterator;
use BEAR\Resource\Exception\AppNamespace;
use Ray\Di\Di\Inject;
use Ray\Di\InstanceInterface;

/**
 * Application resource adapter
 */
class AppAdapter implements AdapterInterface, \IteratorAggregate
{
    /**
     * Application dependency injector
     *
     * @var \Ray\Di\Injector
     */
    private $injector;

    /**
     * Resource adapter namespace
     *
     * @var array
     */
    private $namespace;

    /**
     * Resource adapter path
     *
     * @var array
     */
    private $path;

    /**
     * @var string
     */
    private $resourceDir;

    /**
     * @param InstanceInterface $injector    Application dependency injector
     * @param string            $namespace   Resource adapter namespace
     * @param string            $path        Resource adapter path
     * @param string            $resourceDir Resource root dir path
     *
     * @Inject
     * @throws AppNamespace
     */
    public function __construct(
        InstanceInterface $injector,
        $namespace,
        $path,
        $resourceDir = null
    ) {
        if (!is_string($namespace)) {
            throw new AppNamespace(gettype($namespace));
        }
        $this->injector = $injector;
        $this->namespace = $namespace;
        $this->path = $path;
        $this->resourceDir = $resourceDir;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $parsedUrl = parse_url($uri);
        $className = '';
        $offset = 0;
        while (preg_match('!/([^/]*)!', $parsedUrl['path'], $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $className .= '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $matches[1][0])));
            $offset = $matches[1][1] + strlen($matches[1][0]);
        }

        return $this->injector->getInstance($this->namespace . '\\' . $this->path . $className);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->resourceDir ? new AppIterator($this->resourceDir) : new \ArrayIterator([]);
    }
}
