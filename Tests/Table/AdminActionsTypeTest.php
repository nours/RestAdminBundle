<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Table;

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\TableBundle\Table\TableInterface;

/**
 * Class AdminActionsTypeTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminActionsTypeTest extends AdminTestCase
{
    public function testRenderPrototype()
    {
        /** @var TableInterface $table */
        $table = $this->get('nours_table.factory')->createTable('post', array(
        ));

        $fieldView = $table->createView()->fields['actions'];

        $html = $this->get('nours_table.table_renderer.twig')->renderField($fieldView, 'formatter');

//        echo $html;

        $this->assertNotFalse(strpos($html, '<a href="/posts/__post__/edit">'));
        $this->assertNotFalse(strpos($html, '<a href="/posts/__post__/comments">'));
    }
}