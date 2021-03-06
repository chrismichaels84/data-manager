<?php
namespace Michaels\Manager\Test\Unit\Decoders;

use Michaels\Manager\Decoders\YamlDecoder;
use Symfony\Component\Yaml\Yaml;

class YamlDecoderTest extends \PHPUnit_Framework_TestCase
{
    private $yamlData;
    private $testArrayData;

    /** @var YamlDecoder */
    private $yamlDecoder;

    public function setup()
    {
        $this->testArrayData = [
            'one' => [
                'two' => [
                    'three' => [
                        'true' => true,
                    ]
                ],
                'four' => [
                    'six' => false,
                ]
            ],
            'five' => 5,
            'six' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ]
        ];



        $this->yamlDecoder = new YamlDecoder();
        $this->yamlData = Yaml::dump($this->testArrayData);

    }

    public function test_getting_mime_type()
    {
        $expected = ['yaml','yml'];
        $this->assertEquals($expected, $this->yamlDecoder->getMimeType());

    }

    public function test_yaml_decoding()
    {
        $this->assertEquals($this->testArrayData, $this->yamlDecoder->decode($this->yamlData));
    }
}

