<?php
namespace Pangaea\Test;

use \PHPUnit_Framework_TestCase;
use \Pangaea\Feed;
use \Pangaea\Item;
use \Pangaea\PangaeaException;
use \Pangaea\Attribute\VariantMetaDataAttribute;

class ItemsTest extends PHPUnit_Framework_TestCase
{
    use \Pangaea\Test\FixtureLoadTrait;

    protected $feed;

    protected $item;

    public function setUp()
    {
        $feed = new Feed('2015-01-01 12:34:56');

        $item = new Item('SKU123', '5000000000123');
        $item->setTitle('Sample item');
        $item->setBrand('Brandtastic');
        $item->setDescriptions('Short description', 'Longer description about the item...');
        $item->setTaxCode(20);
        $item->setDates('2015-01-01', '2025-01-01');
        $item->setPublishStatus('UNPUBLISHED');
        $item->setLifecycleStatus('ACTIVE');
        $item->setDimensions(50, 1.5, 74.67, 'CM');
        $item->setWeight(0.5, 'G');
        $item->setPricing(14.99, 9.99, 12.49, '2015-01-01');

        $item->addAttributes('Product', [
            'availability_flag' => true,
            'catalog_id'        => 'TestCatalog',
            'barcode_list'      => ['5000000000123', '5000000000456'],
            'online_from'       => '2015-01-01 12:34:56',
            'stock_quantity'    => 123,
            'profit_margin'     => 12.34,
            'export_excluded'   => null,
            'export_include'    => ''
        ]);

        $item->addVariantMetaData([
            new VariantMetaDataAttribute('colour', 'red',    'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'orange', 'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'yellow', 'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'green',  'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'blue',   'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'indigo', 'LOCATOR'),
            new VariantMetaDataAttribute('colour', 'violet', 'LOCATOR'),
        ]);

        $item->addVariantMetaData([
            new VariantMetaDataAttribute('size', 'XS',  'DEFAULT'),
            new VariantMetaDataAttribute('size', 'S',   'DEFAULT'),
            new VariantMetaDataAttribute('size', 'M',   'DEFAULT'),
            new VariantMetaDataAttribute('size', 'L',   'DEFAULT'),
            new VariantMetaDataAttribute('size', 'XL',  'DEFAULT'),
            new VariantMetaDataAttribute('size', 'XXL', 'DEFAULT'),
        ]);

        $item->addAttributes('Compliance', [
            'over_18_age' => true
        ]);

        // example of some common attributes duplicated in multiple attribute groups, with an addition in the second...
        $common = ['sku' => 'SKU12345', 'is_international' => true];
        $item->addAttributes('MarketInProduct', $common);
        $item->addAttributes('MarketInOffer', array_merge(['addition' => true], $common));

        $item->addAttributes('Offer', [
            'pre_order' => true
        ]);

        $item->setAssets(['1.png', '2.png', '3.png'], 'http://example.com/image');

        $item->setItemLogistics(12345, 12345678, 123456);

        $feed->addItem($item);

        $this->feed = $feed;
        $this->item = $item;
    }

    public function tearDown()
    {
        $this->feed = null;
        $this->item = null;
    }

    public function testItemsXml()
    {
        $sampleXml = $this->loadXmlFixture('items.xml');
        $outputXml = $this->feed->getXml();

        $this->assertXmlStringEqualsXmlString($sampleXml, $outputXml);
    }

    /**
     * @expectedException              \Pangaea\PangaeaException
     * @expectedExceptionMessageRegExp /Invalid publish status ".*"/
     */
    public function testInvalidPublishStatusException()
    {
        $this->item->setPublishStatus('FOOBAR');
    }

    /**
     * @expectedException              \Pangaea\PangaeaException
     * @expectedExceptionMessageRegExp /Invalid lifecycle status ".*"/
     */
    public function testInvalidLifecycleStatusException()
    {
        $this->item->setLifecycleStatus('FOOBAR');
    }

    /**
     * @expectedException               \Pangaea\PangaeaException
     * @expectedExceptionMessageRegExp  /Entity 'bull' not defined/
     */
    public function testProductsInvalidEntitiesException()
    {
        $this->item->setTitle('product number 123 &amp;bull;');

        $this->feed->addItem($this->item);
        $this->feed->save(__DIR__ . '/output/items.xml');
    }

    /**
     * @expectedException         \Pangaea\PangaeaException
     * @expectedExceptionMessage  Variant Meta Data must be an instance of VariantMetaDataAttribute
     */
    public function testProductsVariantMetaDataInvalidObjectException()
    {
        $invalidObject      = new \stdClass();
        $invalidObject->foo = 'bar';

        $this->item->addVariantMetaData($invalidObject);
    }

    public function testSaveItemsXml()
    {
        $this->assertTrue($this->feed->save(__DIR__ . '/output/items.xml'));
    }
}
