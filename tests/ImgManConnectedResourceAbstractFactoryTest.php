<?php
namespace ImgManTest\Apigility;

use MatryoshkaTest\Model\Object\AbstractCollectionTest;
use PHPUnit_Framework_TestCase;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager;

/**
 * Class ImgManConnectedResourceAbstractFactoryTest
 */
class ImgManConnectedResourceAbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function setUp()
    {
        $config = [
            'imgman-apigility' => [
                'imgman-connected' => [
                    'ImgmanApigility\ConnectedResource1' => [
                        'service' => 'ImgMan\Service1'
                    ],
                    'ImgmanApigility\ConnectedResource2' => [],
                    'ImgmanApigility\ConnectedResource3' => [
                        'service' => 'ImgMan\Service3',
                        'idName' => 'testIdName',
                        'blobName' => 'test',
                        'renderBlob' => 'renderBlob'
                    ],
                    'ImgmanApigility\ConnectedResource4' => [
                        'service' => 'ImgMan\Service3',
                        'resource_class' => 'stdClass',
                    ],

                ]
            ]
        ];

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'ImgMan\Apigility\ImgManConnectedResourceAbstractFactory',
                    ],
                    'services' => [
                        'ImgMan\Service1' => new \stdClass(),
                        'ImgMan\Service3' => $this->getMock('ImgMan\Service\ImageService')
                    ]
                ]
            )
        );

        $this->serviceManager->setService('Config', $config);
    }

    public function testHasServiceWithoutConfig()
    {
        $this->assertFalse($this->serviceManager->has('ImgmanApigility\ConnectedResource2'));
    }

    public function testHasService()
    {
        $this->assertTrue($this->serviceManager->has('ImgmanApigility\ConnectedResource1'));
    }

    /**
     * @depends testHasService
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testWrongServiceConfigException()
    {
        $this->serviceManager->get('ImgmanApigility\ConnectedResource1');
    }

    /**
     * @depends testHasService
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    public function testWrongResourceClassConfigException()
    {
        $this->serviceManager->get('ImgmanApigility\ConnectedResource4');
    }

    public function testGetService()
    {
        $this->assertTrue($this->serviceManager->has('ImgmanApigility\ConnectedResource3'));
        $resource =  $this->serviceManager->get('ImgmanApigility\ConnectedResource3');
        $this->assertInstanceOf('ZF\Rest\AbstractResourceListener', $resource);
    }

    public function testEmptyConfig()
    {
        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'ImgMan\Apigility\ImgManConnectedResourceAbstractFactory',
                    ]
                ]
            )
        );
        $this->assertFalse($this->serviceManager->has('ImgmanApigility\ConnectedResource1'));

        $this->serviceManager = new ServiceManager\ServiceManager(
            new ServiceManagerConfig(
                [
                    'abstract_factories' => [
                        'ImgMan\Apigility\ImgManConnectedResourceAbstractFactory',
                    ]
                ]
            )
        );

        $this->serviceManager->setService('Config', []);
        $this->assertFalse($this->serviceManager->has('ImgmanApigility\ConnectedResource1'));
    }
}
