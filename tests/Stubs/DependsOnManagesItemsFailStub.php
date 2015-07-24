<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\DependsOnManagesItemsTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

class DependsOnManagesItemsFailStub
{
    use DependsOnManagesItemsTrait;

    public function check()
    {
        return $this->ensureCanManageItems();
    }
}