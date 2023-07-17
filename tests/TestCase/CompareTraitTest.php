<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use FriendsOfCake\TestUtilities\CompareTrait;
use ReflectionClass;

class CompareTraitTest extends TestCase
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

    public static function htmlInputFilesProvider()
    {
        return self::findFiles('html');
    }

    public static function jsonInputFilesProvider()
    {
        return self::findFiles('json');
    }

    public static function xmlInputFilesProvider()
    {
        return self::findFiles('xml');
    }

    protected static function findFiles($format)
    {
        // phpcs:ignore
        $reflector = new ReflectionClass(__CLASS__);
        $path = dirname($reflector->getFileName()) . '/' . $format . '/';

        $return = [];
        foreach (glob("{$path}*.$format") as $file) {
            $return[str_replace($path, '', $file)] = [$file];
        }

        return $return;
    }
}
