<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Auth\UserProvider;
use Exception;
use Illuminate\Database\Eloquent\Model;

class OpenSsoUserProvider extends AbstractUserProvider implements UserProvider
{

    /**
     * @param array $credentials
     * @return Model|OpenAmUser|string
     * @throws Exception
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

        $authenticateUri = "/". $this->deployUri ."/identity/authenticate";


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
        } else if (strpos($output, 'token.id=') !== false) {

            $tokenId = str_replace('token.id=', '', $output);
            $tokenId = substr($tokenId, 0, - 1);

            $this->tokenId = $tokenId;

            $this->setUser($tokenId);

            curl_close($ch);

        }

        return $this->userModel;
    }


    /**
     * @param string $tokenId
     * @return bool
     * @throws Exception
     */
    protected function isTokenValid($tokenId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->serverAddress . '/'. $this->deployUri .'/identity/isTokenValid?tokenid=' . $tokenId);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

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
            curl_close($ch);
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
        curl_setopt($ch, CURLOPT_URL, $this->serverAddress . '/'. $this->deployUri . '/identity/attributes?' .
            $this->cookieName . '=' . $tokenId);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

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

            curl_close($ch);
        }
    }

    protected function setCookieName($cookieName)
    {
        parent::setCookieName($cookieName);

        if(is_null($this->cookieName)) {
            $ch = curl_init($this->serverAddress . "/". $this->deployUri . "/identity/getCookieNameForToken");

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $output = curl_exec($ch);

            $this->cookieName = trim(preg_replace('/\s\s+/', ' ', str_replace('string=', '', $output)));
        }
    }

    protected function setRealm($realm)
    {
        parent::setRealm($realm);

        if(!preg_match('/^realm=/', $realm)){
            $this->realm = 'realm=/' . $this->realm;
        }
    }
}
