<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities;

use Cake\TestSuite\StringCompareTrait;
use ReflectionClass;

/**
 * Assert methods, comparing to files for:
 *  HTML
 *  JSON
 *  XML
 */
trait CompareTrait
{
    /**
     * Relies upon assertSameAsFile
     */
    use StringCompareTrait;

    /**
     * Asert html is the same as a comparison file
     *
     * @param string $path   partial path to test comparison file
     * @param string $result test result as a string
     * @return void
     */
    public function assertHtmlSameAsFile(string $path, string $result): void
    {
        $indented = $this->indentHtml($result);
        $this->assertSameAsFile($path, $indented);
    }

    /**
     * Assert json is the same as a file
     *
     * Compares the array representation
     *
     * @param string $path   partial path to test comparison file
     * @param mixed  $result test result as an array
     * @return void
     */
    public function assertJsonSameAsFile(string $path, mixed $result): void
    {
        if (!file_exists($path)) {
            $path = $this->_compareBasePath . $path;

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
        $this->_updateComparisons ??= env('UPDATE_TEST_COMPARISON_FILES') ?: false;
        if ($this->_updateComparisons) {
            $indented = json_encode(
                $result,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ) . "\n";
            file_put_contents($path, $indented);
        }

        $expected = json_decode(file_get_contents($path), true);
        $this->assertEquals($expected, $result);
    }

    /**
     * Asert xml is the same as a comparison file
     *
     * @param string $path   partial path to test comparison file
     * @param string $result test result as a string
     * @return void
     */
    public function assertXmlSameAsFile(string $path, string $result): void
    {
        $indented = $this->indentXml($result);
        $this->assertSameAsFile($path, $indented);
    }

    /**
     * If compare base path has not been set, use the test file as the base
     *
     * @return void
     */
    protected function initComparePath(): void
    {
        if ($this->_compareBasePath) {
            return;
        }

        $reflector = new ReflectionClass($this);
        $this->_compareBasePath = str_replace(
            'TestCase',
            'comparisons',
            substr($reflector->getFileName(), 0, -8)
        ) . DIRECTORY_SEPARATOR;
    }

    /**
     * Indent html for consistent whitespace and indentation
     *
     * Start from everything on one line
     * Indent tags
     * Indent atttributes one level more than the tag
     *
     * @param  string $html the html string
     * @return string
     */
    protected function indentHtml(string $html): string
    {
        $html = trim(preg_replace("/\s+/", ' ', $html));

        $counter = 0;
        $callback = function ($match) use (&$counter) {
            $isTag = $match[1][0] === '<';

            $indent = str_repeat('  ', $counter);

            if ($isTag) {
                $match[1] = preg_replace(
                    '@ ([\w-]+="[^"]*")@',
                    "\n  $indent\\1",
                    $match[1]
                );
                $isClosingTag = (bool)$match[2];
                $isSelfClosingTag = (bool)$match[3];

                if ($isClosingTag) {
                    $counter--;
                    $indent = str_repeat('  ', $counter);
                } elseif (!$isSelfClosingTag) {
                    $counter++;
                }
            }

            return $indent . rtrim($match[1]) . "\n";
        };

        return ltrim(preg_replace_callback('@(<(/?)[^>]+?(/?)>|[^<]+)(?:\s*)@', $callback, $html));
    }

    /**
     * Use exactly the same routine as for html
     *
     * However stash the xml header so there isn't an extra level of unwanted
     * indentation
     *
     * @param  string $xml the xml string
     * @return string
     */
    protected function indentXml(string $xml): string
    {
        $header = '';
        $headerPos = strpos($xml, '?>');
        if ($headerPos) {
            $headerPos += 2;
            $header = trim(substr($xml, 0, $headerPos)) . "\n";
            $xml = trim(substr($xml, $headerPos));
        }

        return $header . $this->indentHtml($xml);
    }
}
