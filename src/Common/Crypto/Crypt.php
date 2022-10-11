<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 11:12
 */

namespace Litebase\Common\Crypto;

use Exception;
use Litebase\Common\Exception\InvalidArgumentException;
use Litebase\LitebaseClient;
use phpseclib\Crypt\Hash;

class Crypt
{
    private LitebaseClient $client;

    private $data;

    protected $method;

    protected $options = 0;

    private $iv;

    private $isSecureRandom = true;


    /**
     * Crypt constructor.
     * @param LitebaseClient $client
     * @throws Exception
     */
    public function __construct(LitebaseClient $client)
    {
        $this->client = $client;
        $this->setMethod(256);
    }


    /**
     *  simple encryption Key generating
     * @param string
     * @return string
     * */
    public function getKey()
    {
        $key = $this->client->getClient_secret();

        $hashedkey = hash('SHA512', $key, false);
        $hashedkeylast12 = substr($hashedkey, -8);
        $seckeyadjusted = str_replace(".", "", $key);
        $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 8);

        return $seckeyadjustedfirst12 . $hashedkeylast12;

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
     * @throws Exception
     */
    protected function setMethod($blockSize, $mode = 'CBC'): void
    {
        if ($blockSize === 192 && in_array('', array('CBC-HMAC-SHA1', 'CBC-HMAC-SHA256', 'XTS'))) {
            $this->method = null;
            throw new InvalidArgumentException('Invalid block size and mode compatibility');
        }

        $this->method = 'AES-' . $blockSize . '-' . $mode;
    }

    /**
     * this is the encrypt3Des function that generates an encryption Key.
     * @param $data
     * @param $key
     * @return string
     */
    protected function encrypt3Des($data, $key)
    {
        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encData);

    }

    protected function getIV(): string
    {

        $this->iv = openssl_random_pseudo_bytes(16, $this->isSecureRandom);
        if($this->iv === false || $this->isSecureRandom === false){
            $this->iv = bin2hex($this->generate(8));
        }

        return $this->iv;

    }

    /**
     * Generate a random string of specified length.
     * @param int $length The length of the generated string
     * @param string $characters An optional list of characters to use if no character list is
     *                            specified all valid base64 characters are used.
     * @return string
     */
    public function generate(int $length,
                             string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'): string
    {
        $maxCharIndex = \strlen($characters) - 1;
        $randomString = '';

        while ($length > 0) {
            $randomNumber = \random_int(0, $maxCharIndex);
            $randomString .= $characters[$randomNumber];
            $length--;
        }
        return $randomString;
    }

    /**
     * @param string $message The message to authenticate
     * @param string $password Password to use (defaults to client `secret` )
     * @return string Calculated HMAC
     */
    public function calculateHMAC(string $message, string $password = ''): string
    {
        if ($password === '') {
            $password = $this->client->getClient_secret();
        }

        // Append an "a" behind the password and hash it to prevent reusing the same password as for encryption
        $password = hash('sha512', $password . 'a');

        $hash = new Hash('sha512');
        $hash->setKey($password);
        return $hash->hash($message);
    }


    /**
     * @return string
     * @throws Exception
     */
    protected function encrypt(): string
    {
        if ($this->validateParams()) {
            return openssl_encrypt($this->data, $this->method, $this->getKey(), $this->options, $this->getIV());
        }

        throw new InvalidArgumentException('Invalid params');
    }

    /**
     * @return string
     */
    protected function decrypt(): string
    {
        if ($this->validateParams()) {
            return openssl_decrypt($this->data, $this->method, $this->getKey(), $this->options, $this->iv);
        }

        throw new InvalidArgumentException('Invalid params');
    }

    protected function decrypt3Des($data, $key)
    {
        $data = base64_encode($data);
        return openssl_decrypt($data, 'DES-EDE3', $key);
    }

    /**
     * this is the encryption function that combines the getkey() and encryptDes().
     * @param array $options
     * @return string
     * @throws \JsonException
     */
    public function encryption(array $options)
    {
        $this->data = json_encode($options, JSON_THROW_ON_ERROR);
        //encode the data
        $dat = $this->encrypt();
        $response = json_encode([
            'data'  => base64_encode($dat),
            'iv'    => base64_encode($this->iv),
            'hash'  => base64_encode($this->calculateHMAC($dat . $this->iv, $this->client->getClient_secret())),
        ], JSON_THROW_ON_ERROR);

        return base64_encode($response);
    }

    public function decryption(string $options)
    {
        $payload = base64_decode($options);
        $payload = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $this->iv = base64_decode($payload['iv']);
        $this->data = base64_decode($payload['data']);
        if (!hash_equals($this->calculateHMAC($this->data.$this->iv, $this->client->getClient_secret()), base64_decode($payload['hash']))) {
            throw new InvalidArgumentException('HMAC does not match.');
        }
        $dat = $this->decrypt();

        if($dat === false){
            throw new \Exception('Decryption failed.');
        }
        //encode the data and the
        return $dat;
    }

}