<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Table\Extension;

use Nours\RestAdminBundle\Helper\AdminHelper;
use Nours\TableBundle\Extension\AbstractExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminExtension
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminExtension extends AbstractExtension
{
    private $helper;

    public function __construct(AdminHelper $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'resource' => function(Options $options) {
                return $this->helper->getCurrentResource();
            },
            'class'  => function(Options $options) {
                $resource = $options['resource'];
                return $resource ? $resource->getClass() : null;
            },
            'action' => 'index',
            'url'    => function(Options $options) {
                if ($action = $options['action']) {
                    return $this->helper->generateUrl($action, $options['route_data'], $options['route_params']);
                }
                return null;
            },
            'route_data' => null,
            'route_params' => array()
        ));

        $resolver->setNormalizer('resource', function(Options $options, $value) {
            if (is_string($value)) {
                return $this->helper->getResource($value);
            }
            return $value;
        });
        $resolver->setNormalizer('action', function(Options $options, $value) {
            if (is_string($value)) {
                if ($resource = $options['resource']) {
                    return $resource->getAction($value);
                }
            }
            return null;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getDependency()
    {
        return 'orm';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin';
    }
}