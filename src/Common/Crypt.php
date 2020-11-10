<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 11:12
 */

namespace Litebase\Common;

use Litebase\LitebaseClient;

class Crypt
{
    private LitebaseClient $client;
    private $data;

    protected $method;

    protected $options = 0;
    private $key;


    /**
     * Crypt constructor.
     */
    public function __construct(LitebaseClient $client)
    {
        $this->client = $client;
        $this->setMethod(256);
    }


    /**
     * this is the getKey function that generates an encryption Key for you by passing your Secret Key as a parameter.
     * @param string
     * @return string
     * */

    public function getKey()
    {

        $key = $this->client->getClient_secret();

        $hashedkey = md5($key);

        $hashedkeylast12 = substr($hashedkey, -8);
        $seckeyadjusted = str_replace(".", "", $key);
        $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 8);

        $encryptionkey = $seckeyadjustedfirst12 . $hashedkeylast12;


        return $encryptionkey;

    }

    /**
     * @return bool
     */
    public function validateParams(): bool
    {
        return $this->data !== null && $this->method !== null;
    }

    /**
     * @param $blockSize
     * @param string $mode
     * @throws \Exception
     */
    public function setMethod($blockSize, $mode = 'CBC'): void
    {
        if ($blockSize === 192 && in_array('', array('CBC-HMAC-SHA1', 'CBC-HMAC-SHA256', 'XTS'))) {
            $this->method = null;
            throw new \Exception('Invalid block size and mode compatibility');
        }

        $this->method = 'AES-' . $blockSize . '-' . $mode;
    }

    /**
     * this is the encrypt3Des function that generates an encryption Key.
     * @param string
     * @return string
     * */

    protected function encrypt3Des($data, $key)
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData);


    }

    protected function getIV(): string
    {
        $iv = substr($this->client->getClient_secret(), 0, 8);

        return bin2hex($iv);
    }

    /**
     * @return string
     * @throws \Exception
     * @noinspection EncryptionInitializationVectorRandomnessInspection
     */
    public function encrypt(): string
    {
        if ($this->validateParams()) {
            return openssl_encrypt($this->data, $this->method, $this->getKey(), $this->options, $this->getIV());
        }

        throw new \Exception('Invalide params');
    }

    public function decrypt(): string
    {
        if ($this->validateParams()) {
            return openssl_decrypt($this->data, $this->method, $this->getKey(), $this->options, $this->getIV());
        }

        throw new \Exception('Invalide params');
    }

    protected function decrypt3Des($data, $key)
    {
        $data = base64_encode($data);
        return openssl_decrypt($data, 'DES-EDE3', $key);
    }

    /**
     * this is the encryption function that combines the getkey() and encryptDes().
     * @param string
     * @return string
     * */

    public function encryption(array $options)
    {

        $this->data = json_encode($options);
        //encode the data and the
        return base64_encode($this->encrypt());
    }

    public function decryption(string $options)
    {
        $this->data = base64_decode($options);
        //encode the data and the
        return $this->decrypt();

    }

}