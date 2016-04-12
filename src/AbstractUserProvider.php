<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Auth\Authenticatable as IlluminateAuth;
use Illuminate\Contracts\Auth\UserProvider;
use \Illuminate\Database\Eloquent\Model as Model;

abstract class AbstractUserProvider implements UserProvider
{
    /**
     * $userModel - interface for authenticatable user
     *
     * @var string
     */
    protected $userModel;

    /**
     * $serverAddress - server address to the REST API passed to the constructor
     *
     * @var string
     */
    protected $serverAddress = null;

    /**
     * $deployUri - first uri to REST API interface. Usually either opensso or openam but can be set to a custom one
     * via the config
     *
     * @var string
     */
    protected $deployUri = null;


    /**
     * $realm - realm used for authenticate operation passed to the constructor
     *
     * @var string
     */
    protected $realm = null;

    /**
     * $cookiePath - the path set for the cookie.
     *
     * @var string
     */
    protected $cookiePath = null;
    /**
     * $cookieDomain - the domain set for the cookie.
     *
     * @var string
     */
    protected $cookieDomain = null;

    /**
     * $cookieName - the name for the cookie.
     *
     * @var string
     */
    protected $cookieName = null;

    /**
     * $tokenId - tokenId of cookie
     *
     * @var string
     */
    protected $tokenId = null;

    /**
     * $uid - uid from $tokenId var
     */
    protected $uid;

    /**
     * @var string
     */
    protected $eloquentUidField;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->serverAddress = $config['serverAddress'];
        $this->setDeployUri($config);
        $this->realm = $config['realm'];
        $this->cookiePath = $config['cookiePath'];
        $this->cookieDomain = $config['cookieDomain'];
        $this->cookieName = $config['cookieName'];

        $model = new OpenAmUser();

        if (!is_null($config['eloquentModel'])) {
            $eloquentModel = new $config['eloquentModel'];
        }

        if (isset($eloquentModel) && $eloquentModel instanceof Model) {
            $model = $eloquentModel;
            $this->eloquentUidField = $config['eloquentUidField'];
        }

        $this->userModel = $model;

        $this->getTokenIdFromCookie();
    }


    /**
     * @param mixed $identifier
     * @return \Illuminate\Database\Eloquent\Collection|Model|OpenAmUser|null|string
     */
    public function retrieveById($identifier)
    {

        if (is_int($identifier) && $this->userModel instanceof Model) {
            $userEloquent =  new $this->userModel;

            return $userEloquent->newQuery()->find($identifier);
        }

        $this->tokenId = $identifier;

        if ($this->isTokenValid($this->tokenId)) {
            $this->setUser($this->tokenId);

            return $this->userModel;
        }

        return null;
    }


    /**
     * @param array $credentials
     * @return null
     */
    public function retrieveByCredentials(array $credentials = array())
    {

        //Signing in using token id from cookie
        if (empty($credentials)) {
            $this->retrieveById($this->tokenId);
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param IlluminateAuth $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(IlluminateAuth $user, array $credentials)
    {
        $token = $user->getAuthIdentifier();

        if(is_null($token))
        {
            return false;
        }

        return true;
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return User|null
     */
    public function retrieveByToken($identifier, $token)
    {
        //
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  IlluminateAuth $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(IlluminateAuth $user, $token)
    {
        //
    }

    /**
     * Validate token
     *
     * @param  string $tokenId
     * @return bool
     */
    protected function isTokenValid($tokenId)
    {
        if (!empty($tokenId)) {
            return true;
        }

        return false;
    }

    /**
     * Sets a user attributes received from the REST API to be used by
     * this adapter.
     *
     * @param  string $tokenId The string for REST API token id
     * @return string
     */
    protected function setUser($tokenId)
    {
        $attributes = new \stdClass();

        $attributes->tokenId = $tokenId;

        $this->userModel->setAttributes($attributes);

        $this->assignEloquentDataIfNeeded();
    }

    /**
     * Set the token id from the user's cookie
     *
     * @return AbstractUserProvider
     */
    protected function getTokenIdFromCookie()
    {
        if (isset($_COOKIE[$this->cookieName])) {
            $this->tokenId = $_COOKIE[$this->cookieName];
        }

        return $this;
    }

    protected function assignEloquentDataIfNeeded()
    {
        if ($this->userModel instanceof Model) {
            $userEloquent =  new $this->userModel;

            $userData = $userEloquent->newQuery()
                ->where($this->eloquentUidField, $this->userModel->uid)
                ->first();

            if(!is_null($userData))
            {
                $userData = $userData->toArray();

                foreach ($userData as $eloquentAttributeKey => $attribute) {
                    $this->userModel->$eloquentAttributeKey = $attribute;
                }
            }
        }
    }

    protected function setDeployUri($config)
    {
        if(isset($config['deployUri'])) $this->deployUri = $config['deployUri'];
        if(is_null($this->deployUri)){
            $this->deployUri = 'openam';
            if($config['legacy']){
                $this->deployUri = 'opensso';
            }
        }
    }
}
