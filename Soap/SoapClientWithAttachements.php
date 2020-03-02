<?php

namespace ColissimoWs\Soap;

/**
 * This class can be overridden at your will.
 * Its only purpose is to show you how you can use your own SoapClient client.
 */
class SoapClientWithAttachements extends \SoapClient
{
    /**
     * Final XML request
     * @var string
     */
    public $lastRequest;

    /**
     * @var string
     */
    protected $rawResponse;

    /**
     * @see SoapClientWithAttachements::__doRequest()
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = null)
    {
        /**
         * Colissimo does not support type definition
         */
        $request = str_replace(' xsi:type="ns1:outputFormat"', '', $request);
        $request = str_replace(' xsi:type="ns1:letter"', '', $request);
        $request = str_replace(' xsi:type="ns1:address"', '', $request);
        $request = str_replace(' xsi:type="ns1:sender"', '', $request);

        /**
         * Colissimo returns headers and boundary parts
         */
        $response = parent::__doRequest($this->lastRequest = $request, $location, $action, $version, $oneWay);

        $this->rawResponse = $response;

        /**
         * So we only keep the XML envelope
         */
        $response = substr($response, strpos($response, '<soap:Envelope '), strrpos($response, '</soap:Envelope>') - strpos($response, '<soap:Envelope ') + strlen('</soap:Envelope>'));
        return '<?xml version="1.0" encoding="UTF-8"?>' . trim($response);
    }
    /**
     * Override it in order to return the final XML Request
     * @return string
     * @see SoapClientWithAttachements::__getLastRequest()
     */
    public function __getLastRequest()
    {
        return $this->lastRequest;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }
}
