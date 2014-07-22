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

namespace PHPMentors\FormalBEAR\Framework\Module;

use Doctrine\Common\Inflector\Inflector;
use Ray\Aop\AbstractMatcher;
use Ray\Di\AbstractModule;
use Ray\Di\ModuleStringerInterface;
use Symfony\Component\Config\Definition\Processor;

use PHPMentors\FormalBEAR\Config\ConfigCollection;

abstract class TransformationModule extends AbstractModule implements ConfigAwareInterface
{
    /**
     * @var \PHPMentors\FormalBEAR\Config\ConfigCollection
     */
    private $configCollection;

    /**
     * {@inheritDoc}
     */
    public function __construct(AbstractModule $module = null, AbstractMatcher $matcher = null, ModuleStringerInterface $stringer = null)
    {
        parent::__construct($module, $matcher, $stringer);

        if ($module instanceof ConfigAwareInterface) {
            $this->configCollection = $module->getConfigCollection();
        }
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array('configCollection'));
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigCollection()
    {
        return $this->configCollection;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->transform((new Processor())->processConfiguration(
            $this->createConfiguration(),
            $this->configCollection->resolveParameterPlaceHolders($this->configCollection[ $this->getModuleID() ])
        ));
    }

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    abstract protected function createConfiguration();

    /**
     * @param array $config
     */
    abstract protected function transform(array $config);

    /**
     * @return string
     */
    protected function getModuleID()
    {
        return Inflector::tableize(preg_replace('/module$/i', '', (new \ReflectionClass($this))->getShortName()));
    }
}
