<?php
namespace Bostonspike\CrmLibrary;
/**
 * CrmConnection
 *
 * @package CrmLibrary
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Chris Brazier <chrisb@bostonspike.com>
 */

class CrmConnection {
    private $_client;

    public function __construct($root_url, $username, $password)
    {
        $this->_client = new \GuzzleHttp\Client([
            'base_uri' => $root_url.'/XRMServices/2011/',
            'timeout' => 20.0,
            'auth' => [$username, $password, 'ntlm'],
        ]);
    }

    private function _client_request($method, $uri, $options = null)
    {
        try {
            $response = $this->_client->request($method, $uri, $options);
        } catch (GuzzleHttp\Exception\ServerException $e) {
            throw new \Exception("Server Error: ".$e->getResponse()->getBody());
            return $e->getResponse();
        } catch (GuzzleHttp\Exception\ClientException $e) {
            throw new \Exception("Client Error: ".$e->getResponse()->getBody());
            return $e->getResponse();
        }
        return $response;
    }

    public function doQuery($setName, $filter = null, $orderby = null, $select = null)
    {
        $query = "?";
        if ($filter) { $query.= '&$filter='.$filter; }
        if ($select) { $query.= '&$select='.$select; }
        if ($orderby) { $orderby.= '&$orderby='.$orderby; }
        // https://www.odata.org/documentation/odata-version-2-0/uri-conventions/
        $response = $this->_client_request(
            "GET",
            "OrganizationData.svc/$setName".$query,
            [
                'headers' => ['Accept' => 'application/json'],
            ]
        );
        return json_decode($response->getBody());
    }

    public function doFetch($setName, $guid)
    {
        $response = $this->_client_request(
            "GET",
            "OrganizationData.svc/$setName(guid'$guid')",
            [
                'headers' => ['Accept' => 'application/json'],
            ]
        );
        return json_decode($response->getBody());
    }

    public function doCreate($setName, $data)
    {
        $response = $this->_client_request(
            "POST",
            "OrganizationData.svc/$setName",
            [
                'headers' => ['Accept' => 'application/json'],
                'json' => $data,
            ]
        );
        return json_decode($response->getBody());
    }
}

