<?php

class ConclurerGenderApiRequest extends Wire {

    // API private key
    private $privateKey = null;

    // Values for localization
    protected $localizationType = 'none', $localizationValue;

    // Values for name / ip
    protected $names = array(), $emailAddress = null;

    // HTTP Connector
    protected $http;

    public function __construct($privateKey=null) {
        $this->http = new WireHttp();
        $this->privateKey = $privateKey;
    }

    public function name ($string) {
        $this->names = array($string);
        $this->emailAddress = null;
        return $this;
    }

    public function names (array $names) {
        $this->names = $names;
        $this->emailAddress = null;
        return $this;
    }

    public function email ($string) {
        $this->emailAddress = $string;
        $this->names = array();
        return $this;
    }

    public function emailAddress ($string) { return $this->email($string); }

    public function ipAddress ($address) {
        $this->localizationType = 'ip';
        $this->localizationValue = $address;
        return $this;
    }

    public function currentIpAddress () {
        $this->ipAddress($_SERVER['REMOTE_ADDR']);
        return $this;
    }

    public function language ($localeString) {
        $this->localizationType = 'language';
        $this->localizationValue = $localeString;
        return $this;
    }

    public function country ($countryCode) {
        $this->localizationType = 'country';
        $this->localizationValue = $countryCode;
        return $this;
    }

    public function fetch() {
        $baseUri = 'https://gender-api.com/get';
        $result = $this->http->getJSON($baseUri, true, $this->buildRequestData());

        if (isset($result['errno'])) {
            $object = new ConclurerGenderApiErrorResult();
            $object->data = $result;
            return $object;
        }

        if (count($this->names) > 1) {
            // Multiple Return arrays
            $return = new WireArray();
            foreach ($result['result'] as $set) {
                $object = new ConclurerGenderApiResult();
                $object->data = $set;
                $return->push($object);
            }

            return $return;
        }
        else {
            $object = new ConclurerGenderApiResult();
            $object->data = $result;
            return $object;
        }
    }

    protected function buildRequestData() {
        $data = array();

        // Selector
        if ($this->emailAddress == null) {
            $data['name'] = implode(';', array_slice($this->names, 0, 100));
        }
        else {
            $data['email'] = $this->emailAddress;
        }

        // Localization
        switch ($this->localizationType) {
            case 'none': break;
            default:
                $data[$this->localizationType] = $this->localizationValue;
                break;
        }

        if ($this->privateKey != null) $data['key'] = $this->privateKey;

        return $data;
    }
}