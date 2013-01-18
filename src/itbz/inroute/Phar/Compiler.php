<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute\Phar;

use Symfony\Component\Finder\Finder;
use Phar;

/**
 * Compiles inroute into a phar
 *
 * @package itbz\inroute
 */
class Compiler
{
    /**
     * Compiles inroute into a phar
     *
     * @param string $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'Inroute.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new Phar($pharFile, 0, 'Inroute.phar');
        $phar->setSignatureAlgorithm(Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('*.mustache')
            //->notName('Compiler.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('test')
            ->in(__DIR__ . '/../../../..');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

/*        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('test')
            ->in(__DIR__ . '/../../../../vendor');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }*/

        #$this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));

        $this->addBin($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        //$this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);

        unset($phar);
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR, '', $file->getRealPath());

        //echo $path."\n";

        $content = file_get_contents($file);

        /*if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }*/

        $phar->addFromString($path, $content);
    }

    private function addBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../../../bin/inroute');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/inroute', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    /**
     * Get phar stub
     *
     * @return string
     */
    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('Inroute.phar');

require 'phar://Inroute.phar/bin/inroute';

__HALT_COMPILER();
EOF;
    }
}
