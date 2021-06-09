<?php
/**
 * Created by Bruce Mubangwa on 07 /Nov, 2020 @ 10:45
 */
namespace Litebase;
class LitebaseClient
{
    const LIBVER = "0.1.2";
    const USER_AGENT = "litebase-api-php-client " .self::LIBVER;
    const OAUTH2_AUTH_URL = '';
    const API_BASE_PATH = 'http://broos.cloud';

    /**
     * @var array
     */
    private array $config;

    /**
     * LitebaseClient constructor.
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge(
            [
                'application_name' => '',
                'target_api' => 'v1',
                // Don't change these unless you're working against a special development
                // or testing environment.
                'base_path' => self::API_BASE_PATH,

                'client_id' => '',
                'client_secret' => '',
                'username' => '',
                'redirect_uri' => null,
                'state' => null,

                // Simple API access key, also from the API console. Ensure you get
                // a Server key, and not a Browser key.
                'developer_key' => '',

                // fetch the ApplicationDefaultCredentials, if applicable
                'use_application_default_credentials' => false,
                'signing_key' => null,
                'signing_algorithm' => null,
                'subject' => null,

                // cache config for downstream auth caching
                'cache_config' => [],

                // function to be called when an access token is fetched
                // follows the signature function ($cacheKey, $accessToken)
                'token_callback' => null,
            ],
            $config
        );

    }

    /**
     * Get a string containing the version of the library.
     *
     * @return string
     */
    public function getLibraryVersion()
    {
        return self::LIBVER;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getClient_secret(){
        return $this->config['client_secret'];
    }

    public function getClient_id(){
        return $this->config['client_id'];
    }

    public function getApplication_name(){
        return $this->config['application_name'];
    }

    public function getTarget_api(){
        return $this->config['target_api'];
    }


}
