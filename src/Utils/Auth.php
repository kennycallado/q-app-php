<?php
namespace Src\Utils;

/**
 * Auth
 *
 * @author  foo
 * @license MIT (or other licence)
 */
class Auth
{
    private $url;
    public string $user_id;
    public string $role;
    public string $gAuth;
    public ?string $pAuth;
    public ?object $project;
    public $error = null;

    /**
     * Constructor
     *
     * @param  string $url authentication server URL
     *
     * @return object
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * signup
     * Sign up a new user
     *
     * @param object $credentials
     * @param string $credentials->username
     * @param string $credentials->password
     * @param string $credentials->email
     *
     * @return $this
     */
    public function signup($credentials)
    {
        $body = (object) [
            'username' => $credentials->username,
            'password' => $credentials->password
        ];

        if ($credentials->project) {
            $body->project = $credentials->project;
        }

        $response = $this->requestProcessor('POST', '/signup', json_encode($body));
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;

            return $this;
        }

        // TODO: ?? wait for verification
        // $this->role = $response->role;
        // $this->gAuth = $response->g_token;
        // $this->pAuth = $response->p_token;
        // $this->user_id = $response->id;

        return $this;
    }

    /**
     * signin
     * Sign in an existing user
     *
     * @param object $credentials
     * @param string $credentials->username
     * @param string $credentials->password
     * @param string $ns (default: 'global')
     * @param string $db (default: 'main')
     *
     * @return Auth
     */
    public function signin($credentials)
    {
        $body = (object) [
            'username' => $credentials->username,
            'password' => $credentials->password
        ];

        $response = $this->requestProcessor('POST', '/login', json_encode($body));
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;

            return $this;
        }

        if (isset($response->project)) { $this->project = $response->project; }
        if (isset($response->p_token)) { $this->pAuth = $response->p_token; }
        if (isset($response->role))    { $this->role = $response->role; }

        $this->gAuth = $response->g_token;
        $this->user_id = $response->id;

        return $this;
    }

    // TODO: probably error mean no longer authenticable
    public function refresh()
    {
        $response = $this->requestProcessor('GET', '/refresh', null, $this->gAuth);
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;

            return $this;
        }

        // reset all values
        $this->project  = null;
        $this->user_id  = '';
        $this->gAuth    = '';
        $this->pAuth    = null;
        $this->role     = '';

        if (isset($response->project)) { $this->project = $response->project; }
        if (isset($response->p_token)) { $this->pAuth = $response->p_token; }
        if (isset($response->role))    { $this->role = $response->role; }

        $this->gAuth = $response->g_token;
        $this->user_id = $response->id;

        return $this;
    }

    private function requestProcessor(
        string $method,
        string $path,
        ?string $body,
        string $auth = '',
        $ns = 'global',
        $db = 'main'
    ) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: ' . $auth,
                'Content-Type: application/json',
                'NS: ' . $ns,
                'DB: ' . $db
            ]
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
