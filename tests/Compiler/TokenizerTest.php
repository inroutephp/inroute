<?php
namespace inroute\Compiler;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pathProvider
     */
    public function testMatchRegex($path, array $valids, array $unvalids)
    {
        $tokenizer = new Tokenizer;
        $tokenizer->tokenize($path);
        $regex = $tokenizer->getRegex();

        foreach ($valids as $valid) {
            $this->assertTrue($regex->match($valid), "<$valid> should match <$path>");
        }

        foreach ($unvalids as $unvalid) {
            $this->assertFalse($regex->match($unvalid), "<$unvalid> should not match <$path>");
        }
    }

    public function pathProvider()
    {
        return array(
            array(
                '/path/to/file',
                array('/path/to/file'),
                array('/path', '/path/to/', 'path/to/file')
            ),
            array(
                '/path/{:name}',
                array('/path/foobar', '/path/2347'),
                array('/path/foo/bar', '/pat/2347', 'path/2347')
            ),
            array(
                '/object/{:id:(\d+)}',
                array('/object/2435'),
                array('/object/', '/object/foobar')
            ),
            array(
                '/object/{:id:(\d+)}/{:name:(.+)}',
                array('/object/234/foobar'),
                array('/object/234', '/object/foo/bar')
            )
        );
    }
}
