<?php

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use FriendsOfCake\TestUtilities\CompareTrait;
use \ReflectionClass;

class CompareHelperTest extends TestCase
{
    use CompareTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->initComparePath();
    }

    /**
     * testAssertHtmlSameAsFile
     *
     * @dataProvider htmlInputFilesProvider
     * @param mixed $name
     */
    public function testAssertHtmlSameAsFile($name)
    {
        $input = file_get_contents($name);
        $this->assertHtmlSameAsFile(basename($name), $input);
    }

    /**
     * testAssertJsonSameAsFile
     *
     * @dataProvider jsonInputFilesProvider
     * @param mixed $name
     */
    public function testAssertJsonSameAsFile($name)
    {
        $input = json_decode(file_get_contents($name), true);
        $this->assertJsonSameAsFile(basename($name), $input);
    }

    /**
     * testAssertXmlSameAsFile
     *
     * @dataProvider xmlInputFilesProvider
     * @param mixed $name
     */
    public function testAssertXmlSameAsFile($name)
    {
        $input = file_get_contents($name);
        $this->assertXmlSameAsFile(basename($name), $input);
    }

    public function htmlInputFilesProvider()
    {
        return $this->findFiles('html');
    }

    public function jsonInputFilesProvider()
    {
        return $this->findFiles('json');
    }

    public function xmlInputFilesProvider()
    {
        return $this->findFiles('xml');
    }

    protected function findFiles($format)
    {
        $reflector = new ReflectionClass($this);
        $path = dirname($reflector->getFileName()) . '/' . $format . '/';

        $return = [];
        foreach(glob("{$path}*.$format") as $file){
            $return[str_replace($path, '', $file)] = [$file];
        }

        return $return;
    }
}
