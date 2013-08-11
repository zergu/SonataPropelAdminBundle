<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PropelAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/**
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 */
class SonataPropelAdminExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->fixTemplatesConfiguration($config, $container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('propel.xml');
        $loader->load('filter_types.xml');

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $config);

        // define the templates
        $container->getDefinition('sonata.admin.builder.propel_list')
            ->replaceArgument(1, $config['templates']['types']['list']);

        $container->getDefinition('sonata.admin.builder.propel_show')
            ->replaceArgument(1, $config['templates']['types']['show']);
    }

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function fixTemplatesConfiguration(array $configs, ContainerBuilder $container)
    {
        $defaultConfig = array(
            'templates' => array(
                'types' => array(
                    'list' => array(
                        'array' => 'SonataAdminBundle:CRUD:list_array.html.twig',
                        'boolean' => 'SonataAdminBundle:CRUD:list_boolean.html.twig',
                        'date' => 'SonataAdminBundle:CRUD:list_date.html.twig',
                        'time' => 'SonataAdminBundle:CRUD:list_time.html.twig',
                        'datetime' => 'SonataAdminBundle:CRUD:list_datetime.html.twig',
                        'text' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'trans' => 'SonataAdminBundle:CRUD:list_trans.html.twig',
                        'string' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'smallint' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'bigint' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'integer' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'decimal' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'identifier' => 'SonataAdminBundle:CRUD:list_string.html.twig',
                        'currency' => 'SonataAdminBundle:CRUD:list_currency.html.twig',
                        'percent' => 'SonataAdminBundle:CRUD:list_percent.html.twig',
                    ),
                    'show' => array(
                        'array' => 'SonataAdminBundle:CRUD:show_array.html.twig',
                        'boolean' => 'SonataAdminBundle:CRUD:show_boolean.html.twig',
                        'date' => 'SonataAdminBundle:CRUD:show_date.html.twig',
                        'time' => 'SonataAdminBundle:CRUD:show_time.html.twig',
                        'datetime' => 'SonataAdminBundle:CRUD:show_datetime.html.twig',
                        'text' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'trans' => 'SonataAdminBundle:CRUD:show_trans.html.twig',
                        'string' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'smallint' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'bigint' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'integer' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'decimal' => 'SonataAdminBundle:CRUD:base_show_field.html.twig',
                        'currency' => 'SonataAdminBundle:CRUD:base_currency.html.twig',
                        'percent' => 'SonataAdminBundle:CRUD:base_percent.html.twig',
                    )
                )
            )
        );

        // let's add some magic, only overwrite template if the SonataIntlBundle is enabled
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['SonataIntlBundle'])) {
            $defaultConfig['templates']['types']['list'] = array_merge($defaultConfig['templates']['types']['list'], array(
                'date' => 'SonataIntlBundle:CRUD:list_date.html.twig',
                'datetime' => 'SonataIntlBundle:CRUD:list_datetime.html.twig',
                'smallint' => 'SonataIntlBundle:CRUD:list_decimal.html.twig',
                'bigint' => 'SonataIntlBundle:CRUD:list_decimal.html.twig',
                'integer' => 'SonataIntlBundle:CRUD:list_decimal.html.twig',
                'decimal' => 'SonataIntlBundle:CRUD:list_decimal.html.twig',
                'currency' => 'SonataIntlBundle:CRUD:list_currency.html.twig',
                'percent' => 'SonataIntlBundle:CRUD:list_percent.html.twig',
            ));

            $defaultConfig['templates']['types']['show'] = array_merge($defaultConfig['templates']['types']['show'], array(
                'date' => 'SonataIntlBundle:CRUD:show_date.html.twig',
                'datetime' => 'SonataIntlBundle:CRUD:show_datetime.html.twig',
                'smallint' => 'SonataIntlBundle:CRUD:show_decimal.html.twig',
                'bigint' => 'SonataIntlBundle:CRUD:show_decimal.html.twig',
                'integer' => 'SonataIntlBundle:CRUD:show_decimal.html.twig',
                'decimal' => 'SonataIntlBundle:CRUD:show_decimal.html.twig',
                'currency' => 'SonataIntlBundle:CRUD:show_currency.html.twig',
                'percent' => 'SonataIntlBundle:CRUD:show_percent.html.twig',
            ));
        }

        array_unshift($configs, $defaultConfig);

        return $configs;
    }
}
