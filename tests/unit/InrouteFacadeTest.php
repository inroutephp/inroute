<?php
namespace itbz\inroute;

class InrouteFacadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @ runInSeparateProcess
     */
    public function testFullStack()
    {
        $facade = new InrouteFacade(__DIR__ . '/../../inroute.json');
        
        //$facade->setFiles(__DIR__ . '/../itbz/test/Working.php');
        //$facade->generate();
        //$this->assertEquals("sdf", $facade->generate());

        //$inroute = eval($facade->generate());
        //echo $inroute->dispatch('/foo/yeah', $_SERVER);
    }
}
