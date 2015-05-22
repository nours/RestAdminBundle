<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Action;

use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractAction
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
abstract class AbstractBuilder implements ActionBuilderInterface
{
    protected $defaultOptions;

    /**
     * @param array $defaultOptions
     */
    public function __construct(array $defaultOptions = array())
    {
        $this->defaultOptions   = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function createAction(Resource $resource, array $options = array())
    {
        return new Action($this->getName(), $this->resolveOptions($options));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Resource $resource, UrlGeneratorInterface $generator, $data)
    {

    }

    /**
     * Builds the option resolver and returns resolved options.
     *
     * @param array $options
     * @return array
     */
    protected function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'controller'
        ));
        $resolver->setDefaults(array(
            'template' => null,
            'handlers' => array(),
            'type'     => null
        ));
        $resolver->setDefaults($this->defaultOptions);

        $this->setDefaultOptions($resolver);

        // Handlers are inserted as arrays like [ handler, priority ]
        // normalization will sort them based on the priority
        $resolver->setNormalizer('handlers', function(Options $options, array $value) {
            $priorityQueue = new \SplPriorityQueue();
            foreach ($value as $handler) {
                $priorityQueue->insert($handler[0], $handler[1]);
            }

            return iterator_to_array($priorityQueue);
        });

        return $resolver->resolve($options);
    }
}