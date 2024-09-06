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
 * repository_zatuk phpzatuk class.
 *
 * @package    repository_zatuk
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_zatuk;

use curl;
use moodle_exception;
use Exception;
define ('VISIBLE', 1);
/**
 * Class phpzatuk
 */
class phpzatuk {

    /**
     * @var string $clientid
     */
    public $clientid;
    /**
     * @var string $secret
     */
    public $secret;
    /**
     * @var string $email
     */
    public $xauthtoken;
    /**
     * @var string $apiurl
     */
    public $apiurl;

    /**
     * Main constructor
     * @param string $apiurl
     * @param string $clientid
     * @param string $secret
     * @return void
     */
    public function __construct($apiurl, $clientid, $secret) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');
        // The API Key must be set before any calls can be made.
        $this->apiurl = $apiurl;
        $this->clientid = $clientid;
        $this->secret = $secret;
        $this->xauthtoken = '';
    }
    /**
     * get search url from api.
     * @return string
     */
    public function createsearchapiurl() {
        return $this->apiurl."/api/v1/videos/index";
    }
    /**
     * get listing params from api.
     * @return array
     */
    public function get_listing_params() {
        global $CFG;
        if ($this->apiurl) {
            $tokenurl = $this->apiurl."/api/v1/apis/token";
            $c = new curl();
            try {
                $tokenjson = $c->post($tokenurl, ['key' => $this->clientid, 'secret' => $this->secret, 'domain' => $CFG->wwwroot]);
                $tokeninfo = json_decode($tokenjson);
                if ($tokeninfo->success) {
                    $token = $tokeninfo->token;
                    $params = ['token' => $token];
                } else {

                    $params = ['token' => ''];
                }

                return $params;
            } catch (Exception $e) {
                throw new moodle_exception($e->getMessage());
            }
        } else {
            $params = ['token' => ''];
            return $params;
        }

    }
    /**
     * get zatuk encryptiontoken from api.
     * @param array $url
     * @return array|string
     */
    public function get_encryption_token($url) {
        $searchurl = $this->apiurl."/api/v1/videos/encryption";
        $c = new curl();
        $params = $this->get_listing_params();
        $params['key'] = $this->clientid;
        $params['secret'] = $this->secret;
        $params['uri'] = $url;
        try {
            $content = $c->post($searchurl, $params);
            return $content;
        } catch (Exception $e) {
            throw new moodle_exception($e->getMessage());
        }
    }
    /**
     * get listing url from api.
     * @return string
     */
    public function createlistingapiurl() {
        return $this->apiurl."/api/v1/videos/fvideos";
    }
    /**
     * get zatuk uploaded data from api.
     * @return array|string
     */
    public function get_upload_data() {
        $searchurl = $this->apiurl."/api/v1/videos/uploaddata";
        $c = new curl();
        $params = $this->get_listing_params();
        $params['key'] = $this->clientid;
        $params['secret'] = $this->secret;
        try {
            $content = $c->post($searchurl, $params);
            return $content;
        } catch (Exception $e) {
            throw new moodle_exception($e->getMessage());
        }
    }
    /**
     * get zatuk videos from api.
     * @param array $params
     * @return array
     */
    public function get_videos($params) {
        $searchurl = $this->createsearchapiurl();
        $curlparams = $this->get_listing_params();
        $params = array_merge($curlparams, $params);
        $c = new curl();
        if ($this->apiurl) {
            try {
                $content = $c->post($searchurl, $params);
                $content = json_decode($content, true);
                return $content;

            } catch (Exception $e) {
                throw new moodle_exception($e->getMessage());
            }
        } else {
            return  [];
        }
    }
}
