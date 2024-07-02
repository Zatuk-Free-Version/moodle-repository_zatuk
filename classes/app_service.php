<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * repository_zatuk app_service class.
 *
 * @since      Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk;
defined('MOODLE_INTERNAL') || die();

global $CFG, $USER;
require_once($CFG->libdir . '/filelib.php');
use curl;
/**
 * [app_service description]
 */
class app_service {

    /**
     * @var string $apiurl
     */
    protected $apiurl;

    /**
     * @var array $clientid
     */
    protected $clientid;
    /**
     * @var array $clientsecret
     */
    protected $clientsecret;
    /**
     * @var array $accesstoken
     */
    protected $accesstoken;
    /**
     * @var array $endpoints
     */
    protected $endpoints;
    /**
     * @var array $session
     */
    protected $session;

    /**
     * Main Constructor
     * @param string $clientid
     * @param string $clientsecret
     */
    public function __construct($clientid = "", $clientsecret = "") {
        global $SESSION;
        $this->session = $SESSION;
        $this->endpoints = $this->available_endpoints();
        $this->clientid = $clientid;
        $this->clientsecret = $clientsecret;
    }

    /**
     * Sets Access token to session
     * @param string $token
     */
    protected function set_access_token($token) {
        $this->session->zatuk_access_token = $token;
    }

    /**
     * Retrives Access token from session variable if valid or generates a new one
     * @return string $accesstoken
     */
    protected function get_access_token() {
        if ($this->is_token_expired()) {
            $token = $this->generate_access_token();
            return $token->token;
        } else {
            return $this->session->zatuk_access_token;
        }
    }

    /**
     * Checks if token is expired
     * @return boolean
     */
    protected function is_token_expired() {
        return ($this->has_access_token() && $this->get_expires_at() > time()) ? false : true;
    }

    /**
     * Checks if session has valid access token
     * @return boolean
     */
    protected function has_access_token() {
        return $this->session->zatuk_access_token ? true : false;
    }

    /**
     * Sets token expiry time
     * @param array $time
     * @return null
     */
    protected function set_expires_at($time) {
        $this->session->zatuk_access_token_expires_at = $time;
    }

    /**
     * Gets token expiry time
     * @return int $tokenexpiresat
     */
    protected function get_expires_at() {
        return $this->session->zatuk_access_token_expires_at ? $this->session->zatuk_access_token_expires_at : false;
    }

    /**
     * Triggers an api call and retrives information from api
     * @param string $endpoint
     * @param array $payload
     * @param array $urlparams
     * @param array $headers
     * @return array $response
     */
    protected function make_api_call($endpoint, $payload = [], $urlparams = [], $headers = []) {
        $response = [];
        $c = new curl();
        $data = $this->generate_endpoint_url($endpoint, $urlparams);
        if ($data['auth_required']) {
            $payload['token'] = $this->get_access_token();
            $payload['key'] = $this->clientid;
            $payload['secret'] = $this->clientsecret;
        }
        if (count($headers)) {
            $c->setHeader($headers);
        }
        try {
            $apiresponse = [];
            switch($data['method']) {
                case 'POST':
                    $apiresponse = $c->post($data['url'], $payload);
                break;

                case 'GET':
                    $apiresponse = $c->get($data['url'], $payload);
                break;

                default:
            }
            if (isset($data['response']) && $data['response'] == 'raw') {
                $finalapiresponse = $apiresponse;
            } else {
                $finalapiresponse = json_decode($apiresponse);
            }
            $response = [
                'error'     => false,
                'message'   => '',
                'response'  => $finalapiresponse,
            ];
        } catch (\Exception $e) {
            $response = [
                'error'     => true,
                'message'   => $e->getMessage(),
                'response'  => [],
            ];
        }
        return $response;
    }

    /**
     * Gets endpoint information
     * @param string $endpoint
     * @param array $urlparams
     * @return array $data
     */
    protected function generate_endpoint_url($endpoint, $urlparams = []) {
        $data = $this->endpoints[$endpoint];
        if (count($urlparams)) {
            foreach ($urlparams as $key => $value) {
                $data['url'] = str_replace("{".$key."}", $value, $data['url']);
            }
        }
        return $data;
    }

    /**
     * List of available endpoints
     * @return array $endpoints
     */
    protected function available_endpoints() {
        $this->apiurl = get_config('repository_zatuk', 'zatuk_api_url');
        return [
            'generate_token'    => [
                'url'           => $this->apiurl.'/api/v1/apis/token',
                'method'        => 'POST',
                'auth_required' => false,
            ],
            'upgrade_package'   => [
                'url'           => $this->apiurl.'/api/v1/apis/packages/upgrade',
                'method'        => 'POST',
                'auth_required' => false,
            ],
            'get_videos'        => [
                'url'           => $this->apiurl.'/api/v1/videos/index',
                'method'        => 'POST',
                'auth_required' => true,
            ],
            'get_video'         => [
                'url'           => $this->apiurl.'/api/v2/videos/show/{videoid}',
                'method'        => 'POST',
                'auth_required' => true,
            ],
            'upload_video'      => [
                'url'           => $this->apiurl.'/api/v1/videos/importVideo',
                'method'        => 'POST',
                'auth_required' => true,
            ],
            'encryption_key'    => [
                'url'           => $this->apiurl.'/api/v2/videos/encryption',
                'method'        => 'GET',
                'auth_required' => true,
                'response'      => 'raw',
            ],
        ];
    }

    /**
     * Generates Access token
     * @return array $response
     */
    protected function generate_access_token() {
        global $CFG;
        $payload = [
            "key"       => $this->clientid,
            "secret"    => $this->clientsecret,
            "domain"    => $CFG->wwwroot,
        ];
        $response = $this->make_api_call('generate_token', $payload);
        $response = $response['response'];
        $this->set_access_token($response->token);
        $this->set_expires_at($response->expires_at);
        return $response;
    }

    /**
     * Upgrades packages
     * @param string $name
     * @param string $email
     * @param string $mdltoken
     * @param string $orgname
     * @param string $shortname
     * @return array $response
     */
    public function upgrade_package($name, $email, $mdltoken, $orgname, $shortname) {
        global $CFG;
        $payload = [
           "url"                => $CFG->wwwroot,
           "token"              => $mdltoken,
           "organization_name"  => $orgname,
           "shortname"          => $shortname,
           "email"              => $email,
           "name"               => $name,
           "description"        => "passing as a static variable, need to remove it as mandatory field on zatuk",
        ];
        $response = $this->make_api_call('upgrade_package', $payload);
        return $response;
    }
    /**
     * Get Video Information from Zatuk
     * @param array $params
     * @return array $response
     */
    public function get_videos($params) {
        $payload = [
            'videoids'  => [],
        ];
        $response = $this->make_api_call('get_videos', $payload);
        return $response;
    }

    /**
     * Get Video Information from Zatuk
     * @param string $videoid
     * @return array $response
     */
    public function get_video($videoid) {
        $urlparams = [
            "videoid"   => $videoid,
        ];
        $response = $this->make_api_call('get_video', [], $urlparams);
        return $response;
    }

    /**
     * Gets encryption key for the uploaded video
     * @param string $url
     * @return array $response
     */
    public function get_encryption_key($url) {
        $payload = [
            'uri'   => $url,
        ];
        $response = $this->make_api_call('encryption_key', $payload);
        return $response;
    }

    /**
     * Uploads videos
     * @param array $payload
     * @return array $response
     */
    public function upload_video($payload) {
        $headers = [];
        $headers[] = 'Content-Type:multipart/form-data';
        $response = $this->make_api_call('upload_video', $payload, [], $headers);
        return $response;
    }
}
