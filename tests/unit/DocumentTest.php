<?php

namespace Daric\Tests;

use Daric\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover Document::offsetSet()
     * @cover Document::getData()
     * @cover Document::offsetExists()
     * @cover Document::hasData()
     * @cover Document::offsetUnset()
     * @cover Document::unsetData()
     * @cover Document::offsetGet()
     * @cover Document::getData()
     */
    public function testArrayAccess()
    {
        $doc = new Document();

        $this->assertFalse(isset($doc['key']));

        $doc['key'] = 'value';
        $this->assertArrayHasKey('key', $doc);
        $this->assertEquals('value', $doc['key']);

        $doc['key'] = ['value1', 'value2'];
        $this->assertArrayHasKey('key', $doc);
        $this->assertEquals('value1', $doc['key'][0]);

        unset($doc['key']);
        $this->assertFalse(isset($doc['key']));
    }

    /**
     * @cover Document::addData()
     * @cover Document::setData()
     */
    public function testAddData()
    {
        $doc = new Document();

        $this->assertEquals(0, count($doc->getData()));

        $doc->addData(['key1' => 'val1', 'key2' => 'val2']);
        $this->assertEquals(2, count($doc->getData()));
    }

    /**
     * @cover Document::setData()
     */
    public function testSetData()
    {
        $doc = new Document();

        $this->assertEquals(0, count($doc->getData()));

        $doc->setData('key1', 'value1');
        $this->assertEquals(1, count($doc->getData()));
        $this->assertEquals('value1', $doc->getData('key1'));

        $doc->setData(['key1' => 'value1.1', 'key2' => 'value2']);
        $this->assertEquals(2, count($doc->getData()));
        $this->assertEquals('value1.1', $doc->getData('key1'));
    }

    /**
     * @cover Document::unsetData()
     * @cover Document::addData()
     */
    public function testUnsetData()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $doc = new Document($data);

        $this->assertEquals(3, count($doc->getData()));

        $doc->unsetData();
        $this->assertEquals(0, count($doc->getData()));

        $doc->addData($data);
        $doc->unsetData(['key1', 'key2']);
        $this->assertEquals(1, count($doc->getData()));
        $this->assertFalse($doc->hasData('key1'));
        $this->assertFalse($doc->hasData('key2'));
        $this->assertTrue($doc->hasData('key3'));
    }

    /**
     * @cover Document::getData()
     */
    public function testGetData()
    {
        $doc = new Document(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertEquals(2, count($doc->getData()));
        $this->assertArraySubset(
            ['key1' => 'value1', 'key2' => 'value2'],
            $doc->getData());

        $this->assertNull($doc->getData('key3'));
    }

    /**
     * @cover Document::hasData()
     */
    public function testHasData()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $doc = new Document();

        $this->assertFalse($doc->hasData());

        $doc->addData($data);
        $this->assertTrue($doc->hasData());
    }

    public function testKeys()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $doc = new Document($data);

        $this->assertArraySubset(
            ['key1', 'key2', 'key3'],
            $doc->keys())
        ;
    }
}
