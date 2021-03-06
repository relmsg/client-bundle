<?php
/*
 * This file is a part of Relations Messenger Client Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/client-bundle
 * @link      https://dev.relmsg.ru/packages/client-bundle
 * @copyright Copyright (c) 2018-2020 Relations Messenger
 * @author    Oleg Kozlov <h1karo@relmsg.ru>
 * @license   https://legal.relmsg.ru/licenses/client-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\ClientBundle\DependencyInjection;

use RM\Bundle\ClientBundle\Transport\TransportType;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('relmsg_client');
        $root = $treeBuilder->getRootNode();
        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->append($this->getTransportNode())
                ->append($this->getAuthNode())
            ->end()
        ;
        return $treeBuilder;
    }

    protected function getAuthNode(): NodeDefinition
    {
        $builder = new TreeBuilder('auth');
        $node = $builder->getRootNode();
        $node
            ->canBeDisabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('auto')
                    ->info('Allow automatic authorization on each request when no token is stored')
                    ->defaultTrue()
                ->end()
                ->booleanNode('exception_on_fail')
                    ->info('Allow throwing exception on failed automatic authorization')
                    ->defaultTrue()
                ->end()
                ->scalarNode('app_id')
                    ->defaultValue('%env(string:RM_APP_ID)%')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('app_secret')
                    ->defaultValue('%env(string:RM_APP_SECRET)%')
                    ->cannotBeEmpty()
                ->end()
            ->end();
        return $node;
    }

    protected function getTransportNode(): NodeDefinition
    {
        $builder = new TreeBuilder('transport');
        $node = $builder->getRootNode();
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('type')
                    ->defaultValue(TransportType::HTTP)
                    ->values(TransportType::all())
                ->end()
                ->scalarNode('service')
                    ->defaultNull()
                ->end()
            ->end()
            ->beforeNormalization()
                ->ifString()
                ->then(fn(string $v) => ['type' => $v])
            ->end()
        ;
        return $node;
    }
}
