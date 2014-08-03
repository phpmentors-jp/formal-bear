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

use PHPMentors\FormalBEAR\Doctrine\Dbal\Module\Interceptor\ConnectionOutjectorInterceptor;
use PHPMentors\FormalBEAR\Framework\Module\TransformationModule;

class DoctrineDbalModule extends TransformationModule
{
    /**
     * {@inheritDoc}
     */
    protected function createConfiguration()
    {
        return new DoctrineDbalConfiguration();
    }

    /**
     * {@inheritDoc}
     */
    protected function transform(array $config)
    {
        $this->bind()->annotatedWith('doctrine_dbal_default_persistence_context_id')->toInstance($config['default_context']);
        $this->bind()->annotatedWith('doctrine_dbal_persistence_contexts')->toInstance($config['contexts']);

        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->logicalAnd(
                $this->matcher->annotatedWith('PHPMentors\FormalBEAR\Framework\Annotation\Outject'),
                $this->matcher->annotatedWith('PHPMentors\FormalBEAR\Doctrine\Dbal\Annotation\Connection')
            ),
            [$this->requestInjection('PHPMentors\FormalBEAR\Doctrine\Dbal\Module\Interceptor\ConnectionOutjectorInterceptor')]
        );
    }
}
