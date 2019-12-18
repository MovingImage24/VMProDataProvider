<?php

namespace MovingImage\DataProvider\Tests\VideoCollectionBundle\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\DataProvider\VideoManagerPro;
use PHPUnit\Framework\TestCase;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;

class VideoManagerProTest extends TestCase
{
    public function testGetData()
    {
        $client = $this->getMockBuilder(ApiClientInterface::class)->getMock();

        $dataProvider = new VideoManagerPro($client);

        $vm_id = 5;
        $limit = 4;

        $videoRequestParameters = new VideosRequestParameters();
        $videoRequestParameters
            ->set('include_channel_assignments', true)
            ->set('include_custom_metadata', true)
            ->set('include_keywords', true)
            ->set('limit', $limit);

        $options = ['vm_id' => $vm_id, 'limit' => $limit];
        $arrayCollection = new ArrayCollection();

        $client
            ->expects($this->once())
            ->method('getVideos')
            ->with($vm_id, $videoRequestParameters)
            ->willReturn($arrayCollection);

        $return = $dataProvider->getAll($options);
        $this->assertEquals($arrayCollection, $return);
    }
}