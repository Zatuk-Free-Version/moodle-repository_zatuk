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
 * This file contains the definition for the class zatuk.
 *
 * This class provides all the functionality for the new zatuk repository.
 *
 * @since Moodle 2.0
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('HEX2BIN_WS', " \t\n\r");

/**
 * phpzatuk
 */
class phpzatuk {

    /**
     * @var array $clientid
     */
    public $clientid;
    /**
     * @var array $secret
     */
    public $secret;
    /**
     * @var array $email
     */
    public $email;
    /**
     * @var array $name
     */
    public $name;
    /**
     * @var array $xauthtoken
     */
    public $xauthtoken;
    /**
     * @var array $apiurl
     */
    public $apiurl;

    /**
     * [__construct description]
     * @param array $apiurl
     * @param array $clientid
     * @param array $secret
     */
    public function __construct($apiurl, $clientid, $secret) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');
        // The API Key must be set before any calls can be made.
        $this->apiurl = $apiurl;
        $this->client_id = $clientid;
        $this->secret = $secret;
        $this->xauth_token = '';
    }
    /**
     * [createsearchapiurl description]
     */
    public function createsearchapiurl() {
        return $this->apiurl."/api/v1/videos/index";
    }
    /**
     * [get_listing_params description]
     */
    public function get_listing_params() {
        global $CFG;
        $tokenurl = $this->apiurl."/api/v1/apis/token";
        $c = new \curl();
        $tokenjson = $c->post($tokenurl, ['key' => $this->client_id, 'secret' => $this->secret, 'domain' => $CFG->wwwroot]);
        $tokeninfo = json_decode($tokenjson);
        $token = $tokeninfo->token;
        $params = ['token' => $token];

        return $params;
    }
    /**
     * [get_encryption_token description]
     * @param array $url
     */
    public function get_encryption_token($url) {
        global $CFG;
        $encryptionurl = $this->apiurl."/api/v1/videos/encryption";
        $c = new \curl();
        $params = $this->get_listing_params();
        $params['key'] = $this->client_id;
        $params['secret'] = $this->secret;
        $params['uri'] = $url;
        $content = $c->get($encryptionurl, $params);
        return $content;
    }
    /**
     * [createlistingapiurl description]
     */
    public function createlistingapiurl() {
        return $this->apiurl."/api/v1/videos/fvideos";
    }
    /**
     * [get_upload_data description]
     */
    public function get_upload_data() {
        $searchurl = $this->apiurl."/api/v1/videos/uploaddata";
        $c = new \curl();
        $params = $this->get_listing_params();
        $params['key'] = $this->client_id;
        $params['secret'] = $this->secret;
        $content = $c->post($searchurl, $params);
        return $content;
    }
    /**
     * [get_videos description]
     * @param array $params
     */
    public function get_videos($params) {
        $searchurl = $this->createsearchapiurl();
        $curlparams = $this->get_listing_params();
        $params = array_merge($curlparams, $params);
        $c = new \curl();
        $content = $c->post($searchurl, $params);
        $content = json_decode($content, true);
        return $content;
    }
}
