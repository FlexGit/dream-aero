<?php

namespace AfService;

use Psr\Log\LoggerInterface;

class AfSoapClient extends \SoapClient
{
    protected $logger;

    public function __construct(string $wsdl, array $options = [], LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        parent::__construct($wsdl, $options);
    }

    public function __soapCall($name, $args, $options = null, $inputHeaders = null, &$outputHeaders = null)
    {
        
        $this->logger->info("{$name} REQUEST", $args);

        try {
            $result = parent::__soapCall($name, $args, $options, $inputHeaders, $outputHeaders);
        } catch (\SoapFault $fault) {
            $this->logger->error("{$name} RESPONSE", [$fault->faultcode, $fault->faultstring]);
            trigger_error("Ошибка SOAP: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
            return null;
        }

        $this->logger->info("{$name} RESPONSE", (array) $result);
        return $result;
    }
}
