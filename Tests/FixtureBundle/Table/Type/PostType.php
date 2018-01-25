<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Table\Type;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Table\Field\AdminActionsType;
use Nours\TableBundle\Builder\TableBuilder;
use Nours\TableBundle\Field\Type\TextType;
use Nours\TableBundle\Table\AbstractType;

/**
 * Class PostType
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilder $builder, array $options)
    {
        $builder
            ->add('id', TextType::class)
            ->add('actions', AdminActionsType::class, array(
                'resource' => 'post',
                'actions' => array(
                    'edit',
                    'comment:index'
                ),
                'action_attr' => function(Action $action) {
                    return array('class' => 'btn');
                },
                'action_label' => function(Action $action) {
                    return '<i class="fa fa-' . $action->getConfig('icon', 'question') . '"></i>' . $action->getName();
                }
            ))
        ;
    }

    /**
     * The name of the table type, should be unique in application.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'post';
    }
}