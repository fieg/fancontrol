<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Kernel;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Kernel 
{
    protected $isBooted = false;
    protected $rootDir;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->rootDir = $this->getRootDir();
    }

    /**
     * Boots kernel
     *
     * return void
     */
    public function boot()
    {
        if ($this->isBooted) {
            return;
        }

        $this->container = $this->initializeContainer();

        $this->isBooted = true;
    }

    /**
     * @return mixed
     */
    public function getLoop()
    {
        return $this->container->get('loop');
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

    /**
     * @return ContainerBuilder
     */
    protected function initializeContainer()
    {
        $container = new ContainerBuilder();

        $container->setParameter('kernel.root_dir', $this->rootDir);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        return $container;
    }
}
