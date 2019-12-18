<?php

declare(strict_types=1);

namespace MovingImage\DataProvider;

use MovingImage\Client\VMPro\Entity\VideoRequestParameters;
use MovingImage\Client\VMPro\Entity\VideosRequestParameters;
use MovingImage\Client\VMPro\Interfaces\ApiClientInterface;
use MovingImage\DataProvider\Interfaces\DataProviderInterface;
use MovingImage\DataProvider\Wrapper\Video;

class VideoManagerPro implements DataProviderInterface
{
    /**
     * @var ApiClientInterface
     */
    private $apiClient;

    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $options)
    {
        return $this->apiClient->getVideos($options['vm_id'], $this->createVideosRequestParameters($options));
    }

    /**
     * {@inheritdoc}
     */
    public function getOne(array $options): ?Video
    {
        if (!isset($options['id'])) {
            // Simply fetch the first video from the collection, but without
            // loading all videos inside the collection
            $options['limit'] = 1;

            $videos = $this->getAll($options);

            if (count($videos) === 0) {
                return null;
            }

            $video = $videos[0];

            $embedCode = $this->apiClient->getEmbedCode(
                $options['vm_id'],
                $videos[0]->getId(),
                $options['player_id']
            );
        } else {
            // Retrieve the video by ID straight from the API
            $params = new VideoRequestParameters();
            $params->setIncludeCustomMetadata(true);
            $params->setIncludeKeywords(true);
            $params->setIncludeChannelAssignments(true);

            $video = $this->apiClient->getVideo($options['vm_id'], $options['id'], $params);
            $embedCode = $this->apiClient->getEmbedCode($options['vm_id'], $options['id'], $options['player_id']);
        }

        return new Video($video, $embedCode);
    }

    public function getCount(array $options): int
    {
        return $this->apiClient->getCount($options['vm_id'], $this->createVideosRequestParameters($options));
    }

    private function createVideosRequestParameters(array $options): VideosRequestParameters
    {
        $parameters = new VideosRequestParameters();

        $parameters->setIncludeChannelAssignments(true);
        $parameters->setIncludeCustomMetadata(true);
        $parameters->setIncludeKeywords(true);

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
