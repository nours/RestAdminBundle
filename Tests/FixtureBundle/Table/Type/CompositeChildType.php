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

use Nours\TableBundle\Builder\TableBuilder;
use Nours\TableBundle\Field\Type\TextType;
use Nours\TableBundle\Table\AbstractType;

/**
 * Class CompositeChildType
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CompositeChildType extends AbstractType
{
    public function buildTable(TableBuilder $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
        ;
    }
}