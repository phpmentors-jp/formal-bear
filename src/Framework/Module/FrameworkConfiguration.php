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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FrameworkConfiguration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $appDir;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $namespace
     * @param string $appDir
     */
    public function __construct($namespace, $appDir)
    {
        $this->namespace = $namespace;
        $this->appDir = $appDir;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('framework')
            ->children()
                ->scalarNode('app_name')
                    ->defaultValue($this->namespace)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('app_class')
                    ->defaultValue($this->namespace . '\App')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('tmp_dir')
                    ->defaultValue($this->appDir . '/var/tmp')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('log_dir')
                    ->defaultValue($this->appDir . '/var/log')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('lib_dir')
                    ->defaultValue($this->appDir . '/var/lib')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('resource_dir')
                    ->defaultValue($this->appDir . '/src/Resource')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('named_constants')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->arrayNode('signal_parameters')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
