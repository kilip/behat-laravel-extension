<?php

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
    )
    {
        if(!file_exists($bootstrapFile)){
            throw new \InvalidArgumentException('The bootstrap file: '.$bootstrapFile.' is not exists');
        }
        $this->bootstrapFile = $bootstrapFile;
    }

    /**
     * {@inheritDoc}
     */
    public function createApplication()
    {
        $app =  include  getcwd().'/'.$this->bootstrapFile;

        return $app;
    }
}
