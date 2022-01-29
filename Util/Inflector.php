<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Util;

use Doctrine\Inflector\InflectorFactory;

/**
 * Class Inflector
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
final class Inflector
{
    private static function getInflector()
    {
        static $inflector;

        if (!$inflector) {
            $inflector = InflectorFactory::create()->build();
        }

        return $inflector;
    }

    public static function classify(string $word)
    {
        return self::getInflector()->classify($word);
    }

    public static function tableize(string $word)
    {
        return self::getInflector()->tableize($word);
    }

    public static function pluralize(string $word)
    {
        return self::getInflector()->pluralize($word);
    }
}