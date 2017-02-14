<?php

namespace MovingImage\DataProvider\Tests\VideoCollectionBundle\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\DataProvider\VideoManagerPro;

class VideoManagerProTest extends \PHPUnit_Framework_TestCase
{
    public function testGetData()
    {
        $client = $this->getMockBuilder('MovingImage\Client\VMPro\Interfaces\ApiClientInterface')->getMock();

        $dataProvider = new VideoManagerPro($client);

        $vm_id = 5;
        $limit = 4;

        $videoRequestParameters = new VideosRequestParameters();
        $videoRequestParameters->set('limit', $limit);

        $options = ['vm_id' => $vm_id, 'limit' => $limit];
        $arrayCollection = new ArrayCollection();

        $client
            ->expects($this->once())
            ->method('getVideos')
            ->with($vm_id, $videoRequestParameters)
            ->will($this->returnValue($arrayCollection));

        $return = $dataProvider->getData($options);
        $this->assertEquals($arrayCollection, $return);
    }
}