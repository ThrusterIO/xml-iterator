<?php

namespace Thruster\Component\XMLIterator\Tests;

/**
 * Class XMLReaderTestCase
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class XMLReaderTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // remove any xmlseq stream-wrapper as it might be a left-over from a previous test
        if (in_array('xmlseq', stream_get_wrappers())) {
            stream_wrapper_unregister('xmlseq');
        }

        parent::setUp();
    }


    /**
     * helper method to create data-providers
     *
     * @param array $result
     * @param       $path
     *
     * @return array of arrays with one entry of each filename as string
     */
    protected function addXmlFiles(array $result, $path)
    {
        return $this->addFiles($result, $path, '~\.xml$~');
    }

    /**
     * helper method to create data-providers
     *
     * @param array  $result
     * @param string $path
     * @param string $pattern PCRE pattern matched against basename
     *
     * @return array of arrays with one entry of each filename as string
     */
    protected function addFiles(array $result, $path, $pattern)
    {
        /** @var \FilesystemIterator|\SplFileInfo[] $dir */
        $dir = new \FilesystemIterator($path);
        foreach ($dir as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if (!preg_match($pattern, $file->getBasename())) {
                continue;
            }

            $result[] = array((string) $file);
        }

        return $result;
    }
}
