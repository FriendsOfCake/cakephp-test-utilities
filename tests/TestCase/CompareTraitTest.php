<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use FriendsOfCake\TestUtilities\CompareTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

class CompareTraitTest extends TestCase
{
    use CompareTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->initComparePath();
    }

    #[DataProvider('htmlInputFilesProvider')]
    public function testAssertHtmlSameAsFile(string $name): void
    {
        $input = file_get_contents($name);
        $this->assertHtmlSameAsFile(basename($name), $input);
    }

    #[DataProvider('jsonInputFilesProvider')]
    public function testAssertJsonSameAsFile(string $name): void
    {
        $input = json_decode(file_get_contents($name), true);
        $this->assertJsonSameAsFile(basename($name), $input);
    }

    #[DataProvider('xmlInputFilesProvider')]
    public function testAssertXmlSameAsFile(string $name): void
    {
        $input = file_get_contents($name);
        $this->assertXmlSameAsFile(basename($name), $input);
    }

    public static function htmlInputFilesProvider(): array
    {
        return self::findFiles('html');
    }

    public static function jsonInputFilesProvider(): array
    {
        return self::findFiles('json');
    }

    public static function xmlInputFilesProvider(): array
    {
        return self::findFiles('xml');
    }

    protected static function findFiles(string $format): array
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
