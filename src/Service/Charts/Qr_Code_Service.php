<?php
/**
 * Created by Bruce Mubangwa on 08 /Nov, 2020 @ 16:47
 */

namespace Litebase\Service\Charts;


use Litebase\Common\Http\Exception;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;

class Qr_Code_Service extends LitebaseService
{


    private $hasExected;
    /**
     * @var mixed
     */
    private $newData;
    private array $oldData;

    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/charts/qr';
        $this->rootUrl = 'http://brooshost:8081/cloud/';
        $this->version = 'v1';

        $this->oldData = array();

    }

    /**
     * @param $text
     * @return $this
     */
    public function setText($text): Qr_Code_Service
    {
        $this->oldData['text'] = $text;

        return $this;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size): Qr_Code_Service
    {
        $this->oldData['size'] = $size;

        return $this;
    }

    public function setMargin($margin): Qr_Code_Service
    {
        $this->oldData['margin'] = $margin;

        return $this;
    }

    public function setLogo($imageData): Qr_Code_Service
    {
        $this->oldData['logo'] = $imageData;

        return $this;
    }

    /**
     * @return string|null
     * @throws Exception
     * @throws \JsonException
     */
    public function getImageUrl(): ?string
    {
        if ($this->hasExected) {
            if ($this->newData->status === true) {
                return $this->newData->data;
            }
        }
        $this->_execute();
        return $this->getImageUrl();
    }

    public function getServiceName(): ?string
    {
        if ($this->hasExected) {
            if ($this->newData->status === true) {
                return $this->newData->service;
            }
        }
        $this->_execute();
        return $this->getServiceName();
    }

    public function getProject(): ?string
    {
        if ($this->hasExected) {
            if ($this->newData->status === true) {
                return $this->newData->project;
            }
        }
        $this->_execute();
        return $this->getProject();
    }


    /**
     * @return mixed
     * @throws \JsonException
     */
    public function getStatus(): ?bool
    {
        if ($this->hasExected) {

            return $this->newData->status ?? false;

        }
        $this->_execute();
        return $this->getStatus();
    }


    private function _execute()
    {
        if (isset($this->oldData['text'])) {
            $this->newData = json_decode($this->execute($this->oldData)->raw_body, false, 512, JSON_THROW_ON_ERROR);
            $this->hasExected = true;
        } else {
            throw new Exception('QR code data not set, at least set the Qr code text.');
        }
    }


}