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
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nours\RestAdminBundle\Domain\DomainResource;
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
    public function createAction(DomainResource $resource, array $options = array())
    {
        return new Action($resource, $this->resolveOptions($options));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $data)
    {

    }

    /**
     * Builds the option resolver and returns resolved options.
     *
     * @see \Nours\RestAdminBundle\Loader\ResourceFactory::configureActions
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
            'name'      => function(Options $options) {
                // The name defaults to type
                return $options['type'];
            },
            'type'      => $this->getName(),
            'instance'  => function(Options $options) {
                // If the action needs an instance, it's resource's param name must be used for route building
                // Activated by default on all non bulk actions (bulks do not need single instance, but a collection)
                // For custom action builders, set false as default if there is no specific instance involved in it (and is non bulk)
                // See index or create
                return !$options['bulk'];
            },
            'bulk'      => false,    // If the action is bulk, it should receive and treat a data collection
            'read_only' => false,    // A read only action do not alter or update the data instance
            'template'  => null,
            'handlers'  => array(),
            'fetcher'   => null,
            'action_template'  => null,
            'fetcher_callback' => null
        ));

        $this->setDefaultOptions($resolver);
        $resolver->setDefaults($this->defaultOptions);

        // Handlers are inserted as arrays like [ handler, priority ]
        // normalization will sort them based on the priority
        $resolver->setNormalizer('handlers', function(Options $options, array $value) {
            $priorityQueue = new \SplPriorityQueue();
            foreach ($value as $handler) {
                $priorityQueue->insert($handler[0], $handler[1]);
            }

            return iterator_to_array($priorityQueue);
        });

        try {
            return $resolver->resolve($options);
        } catch (UndefinedOptionsException $e) {
            throw new \DomainException(sprintf(
                "Error while resolving options for action builder %s",
                $this->getName()
            ), 0, $e);
        }
    }
}