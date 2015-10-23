<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Auth\UserProvider;
use Exception;
use Illuminate\Database\Eloquent\Model;

class OpenSsoUserProvider extends AbstractUserProvider implements UserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return $userModel|null
     */
    public function retrieveByCredentials(array $credentials = array())
    {
        if (empty($credentials)) {
            if ($this->isTokenValid($this->tokenId)) {
                $this->setUser($this->tokenId);

                return $this->userModel;
            } else {
                return null;
            }
        }

        $authenticateUri = "/opensso/identity/authenticate";


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=' . $credentials['username'] .
            '&password=' . $credentials['password'] .
            '&uri=' . $this->realm);
        curl_setopt($ch, CURLOPT_URL, $this->serverAddress . $authenticateUri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        if ($output === false) {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $curlError);
        } else {
            $tokenId = str_replace('token.id=', '', $output);
            $tokenId = substr($tokenId, 0, - 1);

            $this->tokenId = $tokenId;

            $this->setUser($tokenId);

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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->serverAddress . '/opensso/identity/isTokenValid?tokenid=' . $tokenId);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        if ($output === false) {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $curlError);
        } else {
            $result = substr($output, 0, - 1);

            if ($result == 'boolean=true') {
                return true;
            } else {
                return false;
            }
        }
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->serverAddress . '/opensso/identity/attributes?' .
            $this->cookieName . '=' . $tokenId);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        if ($output === false) {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $curlError);
        } else {
            $output = explode(chr(10), $output);

            $attributes = new \stdClass();
            foreach ($output as $key => $value) {
                $tokenPattern = "/^userdetails.token.id=/";
                $namePattern = "/^userdetails.attribute.name=/";
                $valuePattern = "/^userdetails.attribute.value=/";

                $tokenMatch = preg_match($tokenPattern, $value);
                $nameMatch = preg_match($namePattern, $value);


                if ($tokenMatch) {
                    $attributeKey = 'tokenId';
                    $attributeValue = preg_replace($tokenPattern, '', $value);
                } elseif ($nameMatch) {
                    $attributeKey = preg_replace($namePattern, '', $value);
                    $attributeValue = preg_replace($valuePattern, '', $output[$key + 1]);
                }


                $this->userModel->$attributeKey = $attributeValue;
            }

            $this->assignEloquentDataIfNeeded();
        }
    }
}
