<?php
namespace Jpietrzyk\VoltDbTools\Request;


class JsonApiRequest extends AbstractRequest
{
    protected function fetchResult($queryString) {
                // create a new cURL resource and set options
        $ch = $this->createCurlResource();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $querystring);

        // Execute the request
        $resRaw = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new CurlException('Error performing request "' . $error . '" No: "' . curl_errno($ch) . '"');
        }
        
        return $resRaw;
    }
    
    protected function createCurlResource() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->dbServer);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }


}