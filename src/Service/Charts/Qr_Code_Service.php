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


    /**
     * Qr_Code_Service constructor.
     * @param LitebaseClient $client
     */
    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/charts/qr';
        $this->rootUrl = LitebaseClient::API_BASE_PATH;
        $this->version = 'v1';
        $this->oldData = array();

    }

    /**
     * sets the data to be encoded in the qr code
     * @param $text
     * @return $this
     */
    public function setText($text): Qr_Code_Service
    {
        $this->oldData['text'] = $text;

        return $this;
    }

    /**
     * sets the image size, the image is a square
     * @param $size
     * @return $this
     */
    public function setSize($size): Qr_Code_Service
    {
        $this->oldData['size'] = $size;

        return $this;
    }

    /**
     * sets the margin for the qr code
     * @param $margin
     * @return $this
     */
    public function setMargin($margin): Qr_Code_Service
    {
        $this->oldData['margin'] = $margin;

        return $this;
    }

    /**
     * sets the logo to be added to the qr code, currently supports base64 encoded images
     * @param $imageData
     * @return $this
     */
    public function setLogo($imageData): Qr_Code_Service
    {
        $this->oldData['logo'] = $imageData;

        return $this;
    }

    /**
     * gets the base64 encoded qrcode image
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

    /**
     * gets the service name, can use this to get more information about the service
     * @return string|null
     * @throws Exception
     */
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


    /**
     * executes our request
     * @throws Exception
     */
    private function _execute()
    {
        if (isset($this->oldData['text'])) {
           $this->execute($this->oldData);
        } else {
            throw new Exception('QR code data not set, at least set the Qr code text.');
        }
    }


}
