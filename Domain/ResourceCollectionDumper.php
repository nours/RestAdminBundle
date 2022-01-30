<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;


/**
 * Class ResourceCollectionDumper
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceCollectionDumper
{
    public function dump(ResourceCollection $collection, $classCacheName): string
    {
        $content = <<<'EOS'
<?php

use Nours\RestAdminBundle\Domain\ResourceCollection;

class
EOS;
        $content .= ' ' . $classCacheName;
        $content .= <<<'EOS'
 extends ResourceCollection
{
    private $serialized = array(
EOS;

        // Dump resources serialization
        foreach ($collection as $resource) {
            $content .= "'" . serialize($resource) . "',\n";
        }

        $content .= <<<'EOS'
    );

    public function __construct()
    {
        foreach ($this->serialized as $serialized) {
            $this->add(unserialize($serialized));
        }
    }
}

EOS;


        return $content;
    }

//    private function dumpResource(DomainResource $resource)
//    {
//
//    }
//
//    private function dumpCollection(ResourceCollection $collection)
//    {
//        return <<<'EOS'
//
//use Nours\RestAdminBundle\Domain\ResourceCollection
//
//class RestResourceCollection extends ResourceCollection
//{
//    private $serialized = [];
//
//    public function __construct()
//    {
//        $this->add
//    }
//
//EOS;
//
//    }
}