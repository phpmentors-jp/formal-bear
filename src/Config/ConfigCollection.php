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

namespace PHPMentors\FormalBEAR\Config;

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ConfigCollection implements \ArrayAccess
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var \Symfony\Component\Config\Resource\ResourceInterface[]
     */
    private $resources = [];

    public function __construct()
    {
        $this->parameterBag = new ParameterBag();
    }

    /**
     * @param  string  $namespace
     * @return boolean
     */
    public function offsetExists($namespace)
    {
        return array_key_exists($namespace, $this->configs);
    }

    /**
     * @param  string $namespace
     * @return array
     */
    public function offsetGet($namespace)
    {
        if (array_key_exists($namespace, $this->configs)) {
            return $this->configs[$namespace];
        } else {
            return [];
        }
    }

    /**
     * @params string $namespace
     * @param array $values
     */
    public function offsetSet($namespace, $values)
    {
        $this->add($namespace, $values);
    }

    /**
     * @params string $namespace
     */
    public function offsetUnset($namespace)
    {
        unset($this->configs[$namespace]);
    }

    /**
     * @param string $namespace
     * @param array  $values
     */
    public function add($namespace, array $values)
    {
        $this->configs[$namespace][] = $values;
    }

    /**
     * @param \Symfony\Component\Config\Resource\ResourceInterface $resource
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter($name, $value)
    {
        $this->parameterBag->set($name, $value);
    }

    /**
     * @param  mixed $value
     * @return mixed
     */
    public function resolveParameterPlaceHolders($value)
    {
        return $this->parameterBag->resolveValue($value);
    }
}
