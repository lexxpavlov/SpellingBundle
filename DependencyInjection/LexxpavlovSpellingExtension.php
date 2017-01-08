<?php

namespace Lexxpavlov\SpellingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LexxpavlovSpellingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lexxpavlov_spelling.entity_class', $config['entity_class']);
        $container->setParameter('lexxpavlov_spelling.user_field', $config['user_field']);
        $container->setParameter('lexxpavlov_spelling.find_by', $config['find_by']);
        $container->setParameter('lexxpavlov_spelling.data_delimiter', $config['data_delimiter']);
        $container->setParameter('lexxpavlov_spelling.error_trans_domain', $config['error_trans_domain']);
        $container->setParameter('lexxpavlov_spelling.security.entity_class', $config['security']['entity_class']);
        $container->setParameter('lexxpavlov_spelling.security.allowed_role', $config['security']['allowed_role']);
        $container->setParameter('lexxpavlov_spelling.security.query_interval', $config['security']['query_interval']);
        $container->setParameter('lexxpavlov_spelling.security.ban_period', $config['security']['ban_period']);
        $container->setParameter('lexxpavlov_spelling.security.ban_check_period', $config['security']['ban_check_period']);
        $container->setParameter('lexxpavlov_spelling.security.ban_count', $config['security']['ban_count']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.yml');
        }
        if ($config['security']['allowed_role']) {
            $loader->load('features/auth-check.yml');
        }
        if ($config['security']['query_interval'] > 0) {
            $loader->load('features/flood-control.yml');
        }
    }
}
