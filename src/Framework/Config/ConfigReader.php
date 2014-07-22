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

namespace PHPMentors\FormalBEAR\Framework\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

use PHPMentors\FormalBEAR\Framework\Config\Loader\YamlFileLoader;

class ConfigReader
{
    /**
     * @param  string                                                   $configFile
     * @param  string                                                   $type
     * @return \PHPMentors\FormalBEAR\Framework\Config\ConfigCollection
     */
    public function read($configFile, $type = null)
    {
        $configCollection = new ConfigCollection();
        $this->createLoader($configCollection)->load($configFile, $type);

        return $configCollection;
    }

    /**
     * @param  \PHPMentors\FormalBEAR\Framework\Config\ConfigCollection $configCollection
     * @return \Symfony\Component\Config\Loader\LoaderInterface
     */
    protected function createLoader(ConfigCollection $configCollection)
    {
        $locator = new FileLocator();

        return new DelegatingLoader(new LoaderResolver(array(
            new YamlFileLoader($configCollection, $locator),
        )));
    }
}
