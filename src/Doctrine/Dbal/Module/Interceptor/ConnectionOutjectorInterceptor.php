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

namespace PHPMentors\FormalBEAR\Doctrine\Dbal\Module\Interceptor;

use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;

use PHPMentors\FormalBEAR\Doctrine\Dbal\Connection\ConnectionFactory;

final class ConnectionOutjectorInterceptor implements MethodInterceptor
{
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationReader;

    /**
     * @var \PHPMentors\FormalBEAR\Doctrine\Dbal\Connection\ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var string
     */
    private $defaultPersistenceContextId;

    /**
     * @param \Doctrine\Common\Annotations\AnnotationReader $annotationReader
     *
     * @Inject
     */
    public function setAnnotationReader(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @Inject
     */
    public function setConnectionFactory(ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * @param string $defaultPersistenceContextId
     *
     * @Inject
     * @Named("doctrine_dbal_default_persistence_context_id")
     */
    public function setDefaultPersistenceContextId($defaultPersistenceContextId)
    {
        $this->defaultPersistenceContextId = $defaultPersistenceContextId;
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        $invocation->proceed();
        $annotation = $this->annotationReader->getMethodAnnotation($invocation->getMethod(), 'PHPMentors\FormalBEAR\Framework\Annotation\PersistenceContext');

        return $this->connectionFactory->create(($annotation === null || empty($annotation->value)) ? $this->defaultPersistenceContextId : $annotation->value);
    }
}
