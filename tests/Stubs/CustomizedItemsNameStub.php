<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class CustomizedItemsNameStub
 * @package Michaels\Manager\Test\Stubs
 */
class CustomizedItemsNameStub implements ManagesItemsInterface
{
    use ManagesItemsTrait;

    protected $thisIsYetAnotherTest;
    protected $dataItemsName = 'thisIsYetAnotherTest';

    public function __construct()
    {
        $this->initManager();
    }

    public function getItemsDirectly()
    {
        return $this->thisIsYetAnotherTest;
    }
}
