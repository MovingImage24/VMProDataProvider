<?php

namespace MovingImage\DataProvider;

use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\DataProvider\Interfaces\DataProviderInterface;

/**
 * Class VideoManagerPro.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
class VideoManagerPro implements DataProviderInterface
{
    /**
     * @var ApiClientInterface
     */
    private $apiClient;

    /**
     * VideoManagerPro constructor.
     *
     * @param ApiClientInterface $apiClient
     */
    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(array $options)
    {
        return $this->apiClient->getVideos($options['vm_id'], $this->createVideosRequestParameters($options));
    }

    /**
     * Converts array into VideosRequestParameters
     *
     * @param array $options
     * @return VideosRequestParameters
     */
    private function createVideosRequestParameters(array $options)
    {
        $parameters = new VideosRequestParameters();

        $queryMethods = [
            'limit'          => 'setLimit',
            'order'          => 'setOrder',
            'search_term'    => 'setSearchTerm',
            'channel_id'     => 'setChannelId',
            'order_property' => 'setOrderProperty'
        ];

        foreach ($queryMethods as $key => $method)
        {
            if (isset($options[$key]))
            {
                $parameters->$method($options[$key]);
            }
        }

        return $parameters;
    }
}
