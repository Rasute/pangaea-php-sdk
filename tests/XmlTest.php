<?php
namespace Pangaea\Test;

use \PHPUnit_Framework_TestCase;
use \Pangaea\Xml;

class XmlTest extends PHPUnit_Framework_TestCase
{
    public function testXmlAttributeTypeBoolean()
    {
        $this->assertEquals(Xml::attributeType(true), 'BOOLEAN');
        $this->assertEquals(Xml::attributeType(false), 'BOOLEAN');
    }

    public function testXmlAttributeTypeDecimal()
    {
        $this->assertEquals(Xml::attributeType(3.14159265359), 'DECIMAL');
    }

    public function testXmlAttributeTypeInteger()
    {
        $this->assertEquals(Xml::attributeType(42), 'INTEGER');
    }

    public function testXmlAttributeTypeDate()
    {
        $this->assertEquals(Xml::attributeType('2015-01-01'), 'DATE');
    }

    public function testXmlAttributeTypeString()
    {
        $this->assertEquals(Xml::attributeType('foobar'), 'STRING');
    }

    public function testXmlEscapeBoolean()
    {
        $this->assertEquals(Xml::escape(true), 'true');
        $this->assertEquals(Xml::escape(false), 'false');
    }

    public function testXmlEscapeString()
    {
        $this->assertEquals(Xml::escape('foobar &bull;'), 'foobar •');
    }
}
