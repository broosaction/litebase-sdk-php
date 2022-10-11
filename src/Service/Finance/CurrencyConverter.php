<?php
/**
 * Created by Bruce Mubangwa on 19 /Apr, 2021 @ 15:00
 */

namespace Litebase\Service\Finance;


use Litebase\Common\Http\Exception;
use Litebase\LitebaseClient;
use Litebase\LitebaseService;

class Currency_Converter_ extends LitebaseService
{


    /**
     * Currency_Converter_Service constructor.
     * @param LitebaseClient $client
     */
    public function __construct(LitebaseClient $client)
    {
        parent::__construct($client);
        $this->endpoint = '/api/finance/converter';
        $this->rootUrl = LitebaseClient::API_BASE_PATH;
        $this->version = 'v1';
        $this->oldData = array();
        $this->hasExected = false;
    }

    /**
     * the currency code from which we are converting from
     * @param $text
     * @return $this
     */
    public function setFromCurrency($text): Currency_Converter_
    {
        $this->oldData['from'] = $text;

        return $this;
    }

    /**
     * the currency code of the currency we want the value to be returned in
     * @param $text
     * @return $this
     */
    public function setToCurrency($text): Currency_Converter_
    {
        $this->oldData['to'] = $text;

        return $this;
    }

    /**
     * use this to let Litebase Cloud detect the user currency and converts to it
     * @param $text
     * @return $this
     */
    public function setToUserCurrency(): Currency_Converter_
    {
        $this->oldData['to'] = 'auto';

        return $this;
    }

    /**
     * the amount we want to convert, must be either int, double or float
     * @param $size
     * @return $this
     */
    public function setAmount($size): Currency_Converter_
    {
        $this->oldData['amount'] = $size;

        return $this;
    }


    /**
     * gets the currency that litebase cloud converted to
     * @return mixed
     * @throws \JsonException
     */
    public function getCurrency(): ?string
    {

        if (!$this->hasExected) {
            $this->_execute();
        }


        if ($this->newData->status === true) {
            return $this->newData->data->currency;
        }

        return $this->getMessage();
    }

    /**
     * gets the new amount
     * @return mixed
     * @throws \JsonException
     */
    public function getAmount(): ?string
    {
        if (!$this->hasExected) {
            $this->_execute();
        }
        if ($this->newData->status === true) {
            return $this->newData->data->amount;
        }

        return $this->getMessage();
    }


    /**
     * executes our request
     * @throws Exception
     */
    private function _execute(): void
    {
        if (isset($this->oldData['from'])) {
            $this->execute($this->oldData);
        } else {
            throw new Exception('Currency converter amount not set.');
        }
    }


}
