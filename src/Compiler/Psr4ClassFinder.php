<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

final class Psr4ClassFinder implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(string $directory, string $prefix)
    {
        $this->directory = $directory;

        if (!preg_match('/\\\$/', $prefix)) {
            $prefix .= '\\';
        }

        $this->prefix = $prefix;
    }

    public function getIterator(): \Generator
    {
        foreach (new \DirectoryIterator($this->directory) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if ($fileInfo->isFile() && $fileInfo->getExtension() == 'php') {
                yield $this->prefix . $fileInfo->getBasename('.php');
            }
            if ($fileInfo->isDir()) {
                yield from new Psr4ClassFinder(
                    $this->directory . '/' . $fileInfo->getBasename(),
                    $this->prefix . $fileInfo->getBasename()
                );
            }
        }
    }
}
