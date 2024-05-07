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
        $query = (object) [
            'username' => $credentials->username,
            'password' => $credentials->password
        ];

        if ($credentials->project) {
            $query->project = $credentials->project;
        }

        $response = $this->requestProcessor('/signup', json_encode($query), 'global', 'main');
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;

            return $this;
        }

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
        $query = (object) [
            'username' => $credentials->username,
            'password' => $credentials->password
        ];

        $response = $this->requestProcessor('/login', json_encode($query), 'global', 'main');
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;

            return $this;
        }

        if (isset($response->project)) {
            $this->project = $response->project;
        }

        $this->user_id = $response->id;
        $this->role = $response->role;
        $this->gAuth = $response->token;

        return $this;
    }

    /**
     * join
     * Join a user into a project
     *
     * @param string $pass // example: '01HJTEBG4Y1EAXPATENCDCT7WW'
     * @param ?string $ns
     * @param ?string $db
     *
     * @return $this
     */
    public function join($pass, $ns = null, $db = null)
    {
        $query = (object) [
            'ns' => $ns ? $ns : $this->project->center,
            'db' => $db ? $db : $this->project->name,
            'pass' => $pass
        ];

        // request and manage errors
        $response = $this->requestProcessor('/join', json_encode($query), $query->ns, $query->db, "Bearer $this->gAuth");
        if (isset($response->error)) {
            // throw new \Exception($response->error);
            $this->error = $response->error;
            return $this;
        }

        $this->pAuth = $response->token;

        return $this;
    }

    /**
     * signout
     * Sign out a user
     *
     * @return $this
     */
    public function signout() {}

    /**
     * refresh
     * Refresh a JWT
     *
     * @param string $JWT
     * @param string $ns (default: 'global')
     * @param string $db (default: 'main')
     */
    public function refresh() {}

    private function requestProcessor($path, $query, $ns = 'global', $db = 'main', $auth = '')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $query,
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
