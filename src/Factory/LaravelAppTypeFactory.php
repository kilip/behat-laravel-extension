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

namespace Behat\LaravelExtension\Factory;

use Behat\LaravelExtension\Contracts\LaravelFactoryContract;

class LaravelAppTypeFactory implements LaravelFactoryContract
{
    use StaticApplicationTrait;

    /**
     * @var string
     */
    private $bootstrapFile;

    public function __construct(
        $bootstrapFile = 'bootstrap/app.php'
    ) {
        if (!file_exists($bootstrapFile)) {
            throw new \InvalidArgumentException('The bootstrap file: '.$bootstrapFile.' is not exists');
        }
        $this->bootstrapFile = $bootstrapFile;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = include $this->bootstrapFile;

        return $app;
    }

    public function __invoke()
    {
        // TODO: write logic here
    }
}
