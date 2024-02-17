<?php

namespace Orchestra\security;

use Orchestra\io\FileHandler;
use Orchestra\http\Request;
use Orchestra\JsonResponse;



/**
 * Class that handles the creation / authentication
 * of sessions
 *  
 * (c) @author Owen Burns
 * 
 * @author Creator-Solutions -> Owen Burns
 * @author Founder-Studios -> Owen Burns
 */
class Session extends JsonResponse
{

    /**
     * @var FileHandler;
     */
    private FileHandler $io;

    /**
     * @var Request
     */
    private Request $req;

    /**
     * @var string
     */
    private string $userSessionFile;

    /**
     * @var array
     */
    private array $response;

    public function __construct()
    {
        $this->io = new FileHandler();
        $this->req = new Request();
    }

    /**
     * Creates a global session for authenticated
     * user in server storage
     */
    public function createFromGlobal($sessionToken, $sessionData)
    {
        if (null !== $sessionData) {
            $sessionDIR = $this->io->getProjectRoot();

            if (null !== $sessionData || !empty($sessionToken)) {
                $sessionFolder = $sessionDIR . "/sessions/";
                if (!file_exists($sessionFolder)) {
                    mkdir($sessionFolder, 0777, true);

                    $this->userSessionFile = $sessionFolder . "$sessionToken.json";
                    file_put_contents($this->userSessionFile, json_encode($sessionData));
                } else {
                    $this->userSessionFile = $sessionFolder . "$sessionToken.json";
                    file_put_contents($this->userSessionFile, json_encode($sessionData));
                }
            } else {
            }
        }
    }

    public function validate_session(array $userData, $sessionToken)
    {
        $this->userSessionFile = $this->getDirectory($sessionToken);
        $sessionContent = @file_get_contents($this->userSessionFile);

        if (!$sessionContent) {
            $this->response[] = ['status' => false, 'message' => 'Could not retrieve session data'];
            return $this->response[0];
        }

        if ($sessionToken === null) {
            $this->response[] = ['status' => false, 'message' => 'Session Token undefined'];
            return $this->response[0];
        }

        if (!array_key_exists('UUID', $userData)) {
            $this->response[] = ['status' => false, 'message' => 'Mismatched data'];
            return $this->response[0];
        }

        $jsonData = json_decode($sessionContent);

        if ($jsonData === null) {
            $this->response[] = ['status' => false, 'message' => 'Invalid Json format'];
            return $this->response[0];
        }

        $sessionTimestamp = $jsonData->metadata->exp;
        $sessionDate = date("Y-m-d H:i:s", $sessionTimestamp);
        $currentDate = date("Y-m-d H:i:s");

        if ($currentDate === $sessionDate) {
            $this->response[] = ['status' => false, 'message' => 'Session has expired'];
            return $this->response[0];
        }

        if ($currentDate !== $sessionDate) {
            $userSessionUUID = $jsonData->metadata->uuid;

            if ($userSessionUUID !== $userData['UUID']) {
                $this->response[] = ['status' => false, 'message' => 'Mismatched data'];
                return $this->response[0];
            }

            $sessionJWT = $jsonData->token;
            $userJWT = $userData['token'];

            if ($sessionJWT !== $userJWT) {
                $this->response[] = ['status' => false, 'message' => 'Session is invalid'];
                return $this->response[0];
            }

            // If none of the conditions above matched, it means the session is valid
            return true;
        }
    }

    public function destroy($sessionToken, $jsonWebToken): bool
    {
        $userToken = $this->req->get('UUID') ?? "";

        /**
         * Get Session file path
         */
        $sessionDIR = $this->io->getProjectRoot();
        $sessionFolder = $sessionDIR . "/sessions/";


        if (null !== $sessionToken) {
            if (null !== $userToken && isset($userToken)) {
                $this->userSessionFile = $sessionFolder . "$sessionToken.json";

                if (file_exists($this->userSessionFile)) {
                    $sessionContent = file_get_contents($this->userSessionFile);

                    if (null !== $sessionContent) {
                        $jsonContent = json_decode($sessionContent);

                        if (null !== $jsonContent) {
                            $sessionID = $jsonContent->session;

                            if ($sessionID === $sessionToken) {
                                $sessionJWT = $jsonContent->token;

                                if ($jsonWebToken === $sessionJWT) {
                                    if (unlink($this->userSessionFile)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Method that checks the user's current authentication status
     * -> Implement more checks to cater for invalid data or objects
     * -> Checks if expiration date is also still valid
     * 
     * @param sessiontoken string
     * @param userData array
     */
    public function checkSessionStatus(string $sessionToken, array $userData)
    {
        $this->userSessionFile = $this->getDirectory($sessionToken);

        if (file_exists($this->userSessionFile)) {
            $sessionContent = @file_get_contents($this->userSessionFile);

            if (!$sessionContent) {
                $this->response[] = ['status' => false, 'message' => 'Could not retrieve session data'];
                return $this->response[0];
            }

            if (!array_key_exists('UUID', $userData)) {
                $this->response[] = ['status' => false, 'message' => 'Mismatched data'];
                return $this->response[0];
            }

            $jsonData = json_decode($sessionContent);

            if ($jsonData === null) {
                $this->response[] = ['status' => false, 'message' => 'Invalid Json format'];
                return $this->response[0];
            }

            $sessionTimestamp = $jsonData->metadata->exp;
            $sessionDate = date("Y-m-d H:i:s", $sessionTimestamp);
            $currentDate = date("Y-m-d H:i:s");

            if ($currentDate !== $sessionDate) {
                $userSessionUUID = $jsonData->metadata->uuid;

                if ($userSessionUUID !== $userData['UUID']) {
                    $this->response[] = ['status' => false, 'message' => 'Mismatched data'];
                    return $this->response[0];
                }

                $sessionJWT = $jsonData->token;
                $userJWT = $userData['token'];

                if ($sessionJWT !== $userJWT) {
                    $this->response[] = ['status' => false, 'message' => 'Session is invalid'];
                    return $this->response[0];
                }

                // If none of the conditions above matched, it means the session is valid
                return true;
            }
        }
    }

    public function getSessionStorage($sessionToken)
    {
        $sessionFile = $this->getDirectory($sessionToken);
        $sessionContent = @file_get_contents($sessionFile);

        return json_decode($sessionContent, true);
    }

    public function getDirectory($sessionToken)
    {
        $sessionDIR = $this->io->getProjectRoot();
        $sessionFolder = $sessionDIR . "/sessions/";

        return $sessionFolder . "$sessionToken.json";
    }
}
