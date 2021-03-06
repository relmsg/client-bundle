<?php
/*
 * This file is a part of Relations Messenger Client Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/client-bundle
 * @link      https://dev.relmsg.ru/packages/client-bundle
 * @copyright Copyright (c) 2018-2020 Relations Messenger
 * @author    Oleg Kozlov <h1karo@relmsg.ru>
 * @license   https://legal.relmsg.ru/licenses/client-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\ClientBundle\Security\Config;

use ReflectionClass;
use RM\Component\Client\ClientInterface;
use Symfony\Component\Config\FileLocator as BaseFileLocator;

/**
 * Class FileLocator
 *
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class FileLocator extends BaseFileLocator
{
    public function __construct()
    {
        $reflect = new ReflectionClass(ClientInterface::class);
        $packageDir = dirname($reflect->getFileName(), 2);
        parent::__construct($packageDir);
    }
}
