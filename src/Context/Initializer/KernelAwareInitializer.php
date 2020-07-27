<?php

/*
 * This file is part of the Behat\LaravelExtension project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Behat\LaravelExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KernelAwareInitializer implements ContextInitializer, EventSubscriberInterface
{
    public function initializeContext(Context $context)
    {
        // TODO: Implement initializeContext() method.
    }

    public static function getSubscribedEvents()
    {
        return [];
    }
}
