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

namespace PHPMentors\FormalBEAR\Doctrine\Dbal\Module;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class DoctrineDbalConfiguration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private static $DEFAULT_CONTEXT = 'default';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('doctrine_dbal')
            ->addDefaultsIfNotSet()
            ->validate()
                ->always(function ($v) {
                    if (count(array_keys($v['contexts'])) == 0) {
                        foreach ($v as $key => $value) {
                            if (!in_array($key, ['contexts', 'default_context'])) {
                                $v['contexts'][self::$DEFAULT_CONTEXT][$key] = $value;
                            }
                        }
                    }

                    if ($v['driver'] == 'pdo_sqlite') {
                        if (!array_key_exists('path', $v)) throw new \InvalidArgumentException('The child node "path" at path "doctrine_dbal" must be configured.');
                        if ($v['path'] === null) throw new \InvalidArgumentException('The path "doctrine_dbal.path" cannot contain an empty value, but got null.');
                        if ($v['path'] === '') throw new \InvalidArgumentException('The path "doctrine_dbal.path" cannot contain an empty value, but got "".');
                    }

                    return $v;
                })
            ->end()
            ->children()
                ->scalarNode('driver')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('host')
                    ->defaultNull()
                ->end()
                ->scalarNode('port')
                    ->defaultNull()
                ->end()
                ->scalarNode('dbname')
                    ->defaultNull()
                ->end()
                ->scalarNode('user')
                    ->defaultNull()
                ->end()
                ->scalarNode('password')
                    ->defaultNull()
                ->end()
                ->scalarNode('charset')
                    ->defaultNull()
                ->end()
                ->scalarNode('path')
                    ->defaultNull()
                ->end()
                ->scalarNode('default_context')
                    ->defaultValue(self::$DEFAULT_CONTEXT)
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('contexts')
                    ->prototype('scalar')
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
