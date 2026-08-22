<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Tests\IntegrationTestCase;

class BoletoCollectionTest extends IntegrationTestCase
{
    public function gerarUnicoBoletoTest() {        
        $collection = new BoletoCollection(self::$enviroment);

        $boleto = Boleto::fromArray(
            
        );

        $collection->gerarUnicoBoleto('101004', $boleto);

        $this->addToAssertionCount(1);
    }
}
