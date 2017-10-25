<?php

namespace MovingImage\DataProvider;

use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\DataProvider\Interfaces\DataProviderInterface;
use MovingImage\DataProvider\Wrapper\Video;

/**
 * Class VideoManagerProSearch.
 *
 * @author Plotkin Konstantin <constantin.plotkin@movingimage.com>
 */
class VideoManagerProSearch implements DataProviderInterface
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

    /*
     * @param array $options
     */
    protected function searchVideos(array $options)
    {
        return $this->apiClient->searchVideos($options['vm_id'], $this->createVideosRequestParameters($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $options)
    {
        return $this->searchVideos($options)->getVideos();
    }

    /**
     * {@inheritdoc}
     */
    public function getOne(array $options)
    {
        if (!isset($options['id'])) {
            // Simply fetch the first video from the collection, but without
            // loading all videos inside the collection
            $options['limit'] = 1;

            $videos = $this->getAll($options);

            if (count($videos) === 0) {
                return null;
            }

            $video = array_shift($videos);

        } else {

            // Retrieve the video by ID straight from the API
            $params = new VideoRequestParameters();
            $video = $this->apiClient->getVideo($options['vm_id'], $options['id'], $params);
        }

        $embedCode = $this->apiClient->getEmbedCode($options['vm_id'], $video->getId(), $options['player_id']);

        return new Video($video, $embedCode);
    }

    /**
     * CURRENTLY NOT IMPLEMENTED.
     *
     * @param array $options
     *
     * @return int
     */
    public function getCount(array $options)
    {
        return $this->searchVideos($options)->getTotalCount();
    }

    /**
     * Converts array into VideosRequestParameters.
     *
     * @param array $options
     *
     * @return VideosRequestParameters
     */
    private function createVideosRequestParameters(array $options)
    {
        $parameters = new VideosRequestParameters();

        $queryMethods = [
            'limit' => 'setLimit',
            'order' => 'setOrder',
            'search_term' => 'setSearchTerm',
            'search_field' => 'setSearchInField',
            'channel_id' => 'setChannelId',
            'order_property' => 'setOrderProperty',
            'offset' => 'setOffset',
        ];

        foreach ($queryMethods as $key => $method) {
            if (isset($options[$key])) {
                $parameters->$method($options[$key]);
            }
        }

        return $parameters;
    }
}