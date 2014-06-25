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

namespace PHPMentors\FormalBEAR\Module;

use Ray\Di\Scope;

class ResourceClientModule extends \BEAR\Resource\Module\ResourceClientModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->bind('BEAR\Resource\SchemeCollectionInterface')->toProvider('PHPMentors\FormalBEAR\Module\Provider\SchemeCollectionProvider')->in(Scope::SINGLETON);
    }
}
