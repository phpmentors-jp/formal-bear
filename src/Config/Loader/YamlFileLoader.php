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

/*
 * Copyright (c) 2004-2014 Fabien Potencier <fabien@symfony.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace PHPMentors\FormalBEAR\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Parser;

use PHPMentors\FormalBEAR\Config\ConfigCollection;

/**
 * YamlFileLoader loads YAML files service definitions.
 *
 * The YAML format does not support anonymous services (cf. the XML loader).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author KUBO Atsuhiro <kubo@iteman.jp>
 * @see https://github.com/symfony/symfony/blob/2314328b163a492caf7958ce1b108520687323e7/src/Symfony/Component/DependencyInjection/Loader/YamlFileLoader.php
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @var \PHPMentors\FormalBEAR\Config\ConfigCollection
     */
    private $configCollection;

    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    private $parser;

    /**
     * @param \PHPMentors\FormalBEAR\Config\ConfigCollection $configCollection
     * @param \Symfony\Component\Config\FileLocatorInterface $locator
     */
    public function __construct(ConfigCollection $configCollection, FileLocatorInterface $locator)
    {
        parent::__construct($locator);

        $this->configCollection = $configCollection;
        $this->parser = new Parser();
    }

    /**
     * Loads a Yaml file.
     *
     * @param mixed  $file The resource
     * @param string $type The resource type
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $content = $this->loadFile($path);

        $this->configCollection->addResource(new FileResource($path));

        // empty file
        if (null === $content) {
            return;
        }

        // imports
        $this->parseImports($content, $path);

        // parameters
        if (isset($content['parameters'])) {
            foreach ($content['parameters'] as $key => $value) {
                $this->configCollection->setParameter($key, $value);
            }
        }

        // extensions
        $this->loadFromExtensions($content);
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return bool true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * Parses all imports
     *
     * @param array  $content
     * @param string $file
     */
    private function parseImports($content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        foreach ($content['imports'] as $import) {
            $this->setCurrentDir(dirname($file));
            if (is_string($import)) {
                $this->import($import, null, false, $file);
            } else {
                $this->import($import['resource'], null, isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false, $file);
            }
        }
    }

    /**
     * Loads a YAML file.
     *
     * @param string $file
     *
     * @return array The file content
     */
    protected function loadFile($file)
    {
        if (!stream_is_local($file)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
        }

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        return $this->validate($this->parser->parse(file_get_contents($file)), $file);
    }

    /**
     * Validates a YAML file.
     *
     * @param mixed  $content
     * @param string $file
     *
     * @return array
     *
     * @throws InvalidArgumentException When service file is not valid
     */
    private function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
        }

        foreach (array_keys($content) as $namespace) {
            if (in_array($namespace, ['imports', 'parameters'])) {
                continue;
            }
        }

        return $content;
    }

    /**
     * Loads from Extensions
     *
     * @param array $content
     */
    private function loadFromExtensions($content)
    {
        foreach ($content as $namespace => $values) {
            if (in_array($namespace, ['imports', 'parameters'])) {
                continue;
            }

            if (!is_array($values)) {
                $values = [];
            }

            $this->configCollection->add($namespace, $values);
        }
    }
}
