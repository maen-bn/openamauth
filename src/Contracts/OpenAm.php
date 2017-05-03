<?php

namespace Maenbn\OpenAmAuth\Contracts;

interface OpenAm
{
    /**
     * @return string
     */
    public function getTokenId();

    /**
     * @param string $tokenId
     * @return \Maenbn\OpenAmAuth\OpenAm
     */
    public function setTokenId($tokenId);

    /**
     * @return string
     */
    public function getUid();

    /**
     * @param string $uid
     * @return \Maenbn\OpenAmAuth\OpenAm
     */
    public function setUid($uid);

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password);

    /**
     * Validate a user's session. Requires tokenId to be set. Can be done via setTokenId method
     *
     * @return bool
     * @throws \Exception
     */
    public function validateTokenId();

    /**
     * @return \stdClass
     */
    public function getUser();

    /**
     * Obtain an authenticated user's details. Make sure to set a tokenId and uid via the
     * setTokenId and setUid methods respectively
     *
     * @return \Maenbn\OpenAmAuth\OpenAm
     */
    public function setUser();

    /**
     * Logout authenticated user. Make sure to set a tokenId via setTokenId method
     *
     * @return bool
     */
    public function logout();
}