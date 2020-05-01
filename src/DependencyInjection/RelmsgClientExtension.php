<?php
/**
 * This file is a part of Relations Messenger Client Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/client-bundle
 * @link      https://dev.relmsg.ru/packages/client-bundle
 * @copyright Copyright (c) 2018-2020 Relations Messenger
 * @author    h1karo <h1karo@outlook.com>
 * @license   https://legal.relmsg.ru/licenses/client-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\ClientBundle\DependencyInjection;

use Exception;
use RM\Bundle\ClientBundle\Transport\TransportType;
use RM\Component\Client\Transport\HttpTransport;
use RM\Component\Client\Transport\TransportInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use UnexpectedValueException;

/**
 * Class RelmsgClientExtension
 *
 * @package RM\Bundle\ClientBundle\DependencyInjection
 * @author  h1karo <h1karo@outlook.com>
 */
class RelmsgClientExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('client.yaml');
        $loader->load('transports.yaml');
        $loader->load('hydrators.yaml');

        if ($config['transport']['service'] === null) {
            $type = new TransportType($config['transport']['type']);
            $class = $this->getTransportClass($type);
            $this->registerOrAlias($container, TransportInterface::class, $class);
        }
    }

    protected function getTransportClass(TransportType $type): string
    {
        if ($type->is(TransportType::HTTP)) {
            return HttpTransport::class;
        }

        throw new UnexpectedValueException(sprintf('Transport %s is not supported.', $type->getName()));
    }

    private function registerOrAlias(ContainerBuilder $container, string $alias, string $class): void
    {
        if ($container->has($class)) {
            $container->setAlias($alias, $class);
        } else {
            $container->register($alias, $class);
        }
    }
}
