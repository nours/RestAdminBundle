<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Helper;

use Nours\RestAdminBundle\Helper\AdminHelper;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminHelperTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminHelperTest extends AdminTestCase
{
    /**
     * @var AdminHelper
     */
    private $helper;

    public function setUp()
    {
        $this->helper = $this->get('rest_admin.helper');
    }

    /**
     * getAction returns an action instance
     */
    public function testGetAction()
    {
        $action = $this->helper->getAction('post.comment:get');

        $this->assertEquals('post.comment:get', $action->getFullName());
    }

    /**
     * Generate post index url
     */
    public function testGenerateUrl()
    {
        $this->loadFixtures();

        $url = $this->helper->generateUrl('post:index');

        $this->assertEquals('/posts', $url);
    }

    /**
     * Generate post index url
     */
    public function testGenerateUrlWithParams()
    {
        $this->loadFixtures();

        $url = $this->helper->generateUrl('post:index', null, array(
            'foo' => 'bar'
        ));

        $this->assertEquals('/posts?foo=bar', $url);
    }

    /**
     * Generate post get url
     */
    public function testGenerateUrlWithData()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $url = $this->helper->generateUrl('post:get', $post);

        $this->assertEquals('/posts/1', $url);
    }

    /**
     * Generate comment create url
     */
    public function testGenerateUrlForParent()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $url = $this->helper->generateUrl('post.comment:create', $post);

        $this->assertEquals('/posts/1/comments/create', $url);
    }

    /**
     * Generate comment edit url
     */
    public function testGenerateUrlForInstance()
    {
        $this->loadFixtures();
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $url = $this->helper->generateUrl('post.comment:edit', $comment);

        $this->assertEquals('/posts/1/comments/1/edit', $url);
    }

    /**
     * Generate comment edit url
     */
    public function testGenerateUrlForCompositeEntity()
    {
        $this->loadFixtures();
        $composite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'first'
        ));

        $url = $this->helper->generateUrl('composite:edit', $composite);

        $this->assertEquals('/composites/1/first/edit', $url);
    }

    /**
     * Generate comment edit url
     */
    public function testGenerateUrlForCompositeEntityChild()
    {
        $this->loadFixtures();
        $child = $this->getEntityManager()->getRepository('FixtureBundle:CompositeChild')->findOneBy(array(
            'id' => 1
        ));

        $url = $this->helper->generateUrl('composite.composite_child:get', $child);

        $this->assertEquals('/composites/1/first/children/1/child', $url);
    }

    /**
     * The helper helps to extract elements from current request :
     *  - action, resource,
     *  - data, parent
     */
    public function testGetCurrentStuff()
    {
        $action = $this->getAdminManager()->getAction('post:index');

        $request = new Request(array(), array(), array(
            'resource' => $action->getResource(),
            'action' => $action,
        ));
        $this->get('request_stack')->push($request);

        $this->assertSame($request, $this->helper->getRequest());
        $this->assertSame($action, $this->helper->getCurrentAction());
        $this->assertSame($action->getResource(), $this->helper->getCurrentResource());
    }

    /**
     */
    public function testGetCurrentStuffWithData()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $action = $this->getAdminManager()->getAction('post:get');

        $request = new Request(array(), array(), array(
            'resource' => $action->getResource(),
            'action' => $action,
            'data' => $post
        ));
        $this->get('request_stack')->push($request);

        $this->assertSame($post, $this->helper->getResourceInstance());
        $this->assertSame($action, $this->helper->getCurrentAction());
        $this->assertSame($action->getResource(), $this->helper->getCurrentResource());
        $this->assertNull($this->helper->getResourceCollection());
    }

    /**
     */
    public function testGetCurrentStuffWithSubresource()
    {
        $this->loadFixtures();
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $action = $this->getAdminManager()->getAction('post.comment:publish');

        $request = new Request(array(), array(), array(
            'resource' => $action->getResource(),
            'action' => $action,
            'data' => $comment
        ));
        $this->get('request_stack')->push($request);

        $this->assertSame($comment, $this->helper->getResourceInstance());
        $this->assertSame($comment->getPost(), $this->helper->getResourceParent());
        $this->assertSame($action, $this->helper->getCurrentAction());
        $this->assertSame($action->getResource(), $this->helper->getCurrentResource());
        $this->assertNull($this->helper->getResourceCollection());
    }

    /**
     */
    public function testGetCurrentStuffWithCollection()
    {
        $this->loadFixtures();
        $post1 = $this->getEntityManager()->find('FixtureBundle:Post', 1);
        $post2 = $this->getEntityManager()->find('FixtureBundle:Post', 2);

        $action = $this->getAdminManager()->getAction('post:bulk_delete');

        $request = new Request(array(), array(), array(
            'resource' => $action->getResource(),
            'action' => $action,
            'data' => array($post1, $post2)
        ));
        $this->get('request_stack')->push($request);

        $this->assertNull($this->helper->getResourceInstance());
        $this->assertNull($this->helper->getResourceParent());
        $this->assertEquals(array($post1, $post2), $this->helper->getResourceCollection());
        $this->assertSame($action, $this->helper->getCurrentAction());
        $this->assertSame($action->getResource(), $this->helper->getCurrentResource());
    }
}