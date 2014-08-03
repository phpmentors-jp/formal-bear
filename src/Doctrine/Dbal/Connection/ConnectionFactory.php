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

namespace PHPMentors\FormalBEAR\Doctrine\Dbal\Connection;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

class ConnectionFactory extends Connection
{
    /**
     * @var array
     */
    private $persistenceContexts;

    /**
     * @Inject
     * @Named("doctrine_dbal_persistence_contexts")
     */
    public function __construct(array $persistenceContexts)
    {
        $this->persistenceContexts = $persistenceContexts;
    }

    /**
     * @param  string                    $persistenceContextId
     * @return \Doctrine\DBAL\Connection
     * @throws \LogicException
     */
    public function create($persistenceContextId)
    {
        if (array_key_exists($persistenceContextId, $this->persistenceContexts)) {
            return DriverManager::getConnection($this->persistenceContexts[$persistenceContextId], new Configuration(), new EventManager());
        } else {
            throw new \LogicException(sprintf(
                'The persistent context "%s" is not found. It must be a one of "%s".',
                $persistenceContextId,
                implode(', ', array_keys($this->persistenceContexts))
            ));
        }
    }
}
