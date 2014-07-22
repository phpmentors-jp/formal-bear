<?php
/*
 * Copyright (c) 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of Piece_Flow.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\FormalBEAR\Framework\Module;

use Ray\Aop\AbstractMatcher;
use Ray\Di\AbstractModule;
use Ray\Di\ModuleStringerInterface;

use PHPMentors\FormalBEAR\Framework\Config\ConfigReader;

abstract class EntryModule extends AbstractModule implements ConfigAwareInterface
{
    /**
     * @var \PHPMentors\FormalBEAR\Framework\Config\ConfigCollection
     */
    private $configCollection;

    /**
     * {@inheritDoc}
     */
    public function __construct(AbstractModule $module = null, AbstractMatcher $matcher = null, ModuleStringerInterface $stringer = null)
    {
        parent::__construct($module, $matcher, $stringer);

        $this->configCollection = $this->readConfig(new ConfigReader());
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
     * @param  \PHPMentors\FormalBEAR\Framework\Config\ConfigReader     $configReader
     * @return \PHPMentors\FormalBEAR\Framework\Config\ConfigCollection
     */
    abstract protected function readConfig(ConfigReader $configReader);
}
