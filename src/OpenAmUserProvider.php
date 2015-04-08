<?php namespace Maenbn\OpenAmAuth;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Exception;

class OpenAmUserProvider extends AbstractUserProvider implements UserProvider {

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return $userModel|null
     */
    public function retrieveByCredentials(array $credentials = array())
    {

        if (empty($credentials))
        {
            if ($this->isTokenValid($this->tokenId))
            {
                $this->setUser($this->tokenId);

                return $this->userModel;
            } else
            {
                return null;
            }
        }

        $authenticateUri = "/openam/json/authenticate";

        if (!is_null($this->realm))
        {

            $authenticateUri = "/openam/json/" . $this->realm . "/authenticate";

        }

        $ch = curl_init($this->serverAddress . $authenticateUri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-OpenAM-Username: ' . $credentials['username'],
            'X-OpenAM-Password: ' . $credentials['password'],
            'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $output = curl_exec($ch);

        if ($output === false)
        {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $curlError);
        } else
        {
            $json = json_decode($output);

            $this->tokenId = $json->tokenId;

            $this->setUser($this->tokenId);

            curl_close($ch);

            setrawcookie($this->cookieName, $this->tokenId, 0, $this->cookiePath, $this->cookieDomain);

            return $this->userModel;
        }

    }

    /**
     * Validate OpenAM token
     *
     * @param  string $tokenId
     * @return bool
     */
    protected function isTokenValid($tokenId)
    {

        $ch = curl_init($this->serverAddress . "/openam/json/sessions/" . $tokenId . "?_action=validate");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $output = curl_exec($ch);

        $json = json_decode($output);

        $valid = $json->valid;

        if ($valid)
        {

            $this->uid = $json->uid;

            return true;

        }

        return false;
    }

    /**
     * Sets a user attributes received from the REST API to be used by
     * this adapter.
     *
     * @param  string $tokenId The string for REST API token id
     * @return null
     */
    protected function setUser($tokenId)
    {

        $ch = curl_init($this->serverAddress . "/openam/json/users/" . $this->uid);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
            $this->cookieName . ': ' . $tokenId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $output = curl_exec($ch);

        $attributes = json_decode($output);

        $attributes->tokenId = $tokenId;

        foreach ($attributes as $key => $value)
        {
            $this->userModel->$key = $value;
        }

    }

}