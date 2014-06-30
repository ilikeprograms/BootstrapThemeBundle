<?php

namespace ILP\BootstrapThemeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ILPBootstrapThemeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $themeBase = null;
        $templateBase = null;
        foreach ($configs as $config) {
            if (isset($config['theme_base'])) {
                $themeBase = $config['theme_base'];
            }

            if (isset($config['template_base'])) {
                $templateBase = $config['template_base'];
            }
        }

        if (!$themeBase) { throw new \InvalidArgumentException('The "theme_base" option must be provided, this is so we know where to find theme files'); }
        if (!$templateBase) { throw new \InvalidArgumentException('The "template_base" option must be provided, this is so we know where to find template files'); }

        $container->setParameter(
            'ilp_bootstrap_theme.theme_base',
            $themeBase
        );
        
        $container->setParameter(
            'ilp_bootstrap_theme.template_base', $templateBase
        );
    }
}
