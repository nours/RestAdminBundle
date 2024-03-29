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

use DomainException;
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
    public function __construct(array $defaultOptions = [])
    {
        $this->defaultOptions   = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function createAction(DomainResource $resource, array $options = []): Action
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
    protected function resolveOptions(array $options): array
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
                // If the action needs an instance, its resource's param name must be used for route building
                // Activated by default on all non bulk actions (bulks do not need single instance, but a collection)
                // For custom action builders, set false as default if there is no specific instance involved in it (and is non bulk)
                // See index or create
                return !$options['bulk'];
            },
            'bulk'      => false,    // If the action is bulk, it should receive and treat a data collection
            'read_only' => false,    // A read only action do not alter or update the data instance
            'template'  => null,
            'handlers'  => array(),
            'extra_handlers' => array(),
            'fetcher'   => null,
            'factory'   => null,
            'action_template'  => null,
            'fetcher_callback' => null,
            'handler_action' => null,
            'role' => null
        ));
        $resolver->setAllowedValues('handler_action', array(null, 'create', 'update', 'delete'));

        $this->setDefaultOptions($resolver);
        $resolver->setDefaults($this->defaultOptions);

        // Handlers normalization
        // An extra configuration is used to inject handlers from annotations, as the OptionResolver component
        // cannot merge array lists.
        $resolver->setNormalizer('handlers', function(Options $options, array $value) {
            return array_merge($value, $options['extra_handlers']);
        });

        try {
            return $resolver->resolve($options);
        } catch (UndefinedOptionsException $e) {
            throw new DomainException(sprintf(
                "Error while resolving options for action builder %s",
                $this->getName()
            ), 0, $e);
        }
    }
}