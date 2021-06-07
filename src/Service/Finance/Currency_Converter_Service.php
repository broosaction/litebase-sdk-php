<?php
/**
 * Created by Bruce Mubangwa on 19 /Apr, 2021 @ 15:00
 */

namespace Litebase\Service\Finance;


use Litebase\Common\Http\Exception;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;

class Currency_Converter_Service extends LitebaseService
{


    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/finance/converter';
        $this->rootUrl = 'http://broos.cloud';
        $this->version = 'v1';
        $this->oldData = array();
    }

    /**
     * @param $text
     * @return $this
     */
    public function setFromCurrency($text): Currency_Converter_Service
    {
        $this->oldData['from'] = $text;

        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setToCurrency($text): Currency_Converter_Service
    {
        $this->oldData['to'] = $text;

        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setToUserCurrency(): Currency_Converter_Service
    {
        $this->oldData['to'] = 'zmw';

        return $this;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setAmount($size): Currency_Converter_Service
    {
        $this->oldData['amount'] = $size;

        return $this;
    }


    /**
     * @return mixed
     * @throws \JsonException
     */
    public function getCurrency(): ?string
    {
        if ($this->hasExected) {

            if ($this->newData->status === true) {
                return $this->newData->data->currency;
            }

        }
        $this->_execute();
        return $this->getCurrency();
    }

    private function _execute(): void
    {
        if (isset($this->oldData['from'])) {
            $this->execute($this->oldData);
        } else {
            throw new Exception('Currency converter amount not set.');
        }
    }

    /**
     * @return mixed
     * @throws \JsonException
     */
    public function getAmount(): ?string
    {
        if ($this->hasExected) {

            if ($this->newData->status === true) {
                return $this->newData->data->amount;
            }

        }
        $this->_execute();
        return $this->getAmount();
    }


}