<?php

namespace AfService;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;

class AfService
{
    public const LOGFILE = __DIR__ . '/../logs.log';

    protected $logger;
    protected $urlPcpoints;
    protected $urlCftAfl;
    protected $certKey;
    protected $certPassword;
    protected $partnerId;
    protected $location;
    protected $terminal;

    public function __construct(array $config)
    {
        $this->setLogger();

        $this->logger->info('Init', $config);

        $this->urlPcpoints = $config['URL_PCPOINTS'];
        $this->urlCftAfl = $config['URL_CFT_AFL'];
        $this->certKey = $config['CERTKEY_FILE_PATH'];
        $this->certPassword = $config['CERTKEY_PASSWORD'];
        $this->partnerId = $config['PARTNER_ID'];
        $this->location = $config['LOCATION'];
        $this->terminal = $config['TERMINAL'];
    }

    private function setLogger()
    {
        $logger = new Logger('app');
        $logger->pushHandler(new StreamHandler(self::LOGFILE, Logger::DEBUG));
        $this->logger = $logger;
    }

    private function getSoapClient($url)
    {
        return new AfSoapClient($url, [
            'local_cert' => __DIR__ . '/../' . $this->certKey,
            'passphrase' => $this->certPassword,
            'connection_timeout' => 3
        ], $this->logger);
    }

    public function getConnectionPCPoints()
    {
        static $connection;

        if ($connection) return $connection;

        return $connection = $this->getSoapClient($this->urlPcpoints);
    }

    public function getConnectionCftAfl()
    {
        static $connection;

      //  if ($connection) return $connection;

        return $connection = $this->getSoapClient($this->urlCftAfl);
    }

    public function getInfo2($params)
    {
        $soap = $this->getConnectionPCPoints();

        $params['transaction']['location'] = $this->location;
        $params['transaction']['partnerId'] = $this->partnerId;
        $params['transaction']['terminal'] = $this->terminal;

        return $soap->__soapCall('getInfo2', [$params]);
    }

    public function authPoints($params)
    {
        $soap = $this->getConnectionPCPoints();

        $params['transaction']['location'] = $this->location;
        $params['transaction']['partnerId'] = $this->partnerId;
        $params['transaction']['terminal'] = $this->terminal;

        return $soap->__soapCall('authPoints', [$params]);
    }

    public function refund($params)
    {
        $soap = $this->getConnectionPCPoints();

        $params['transaction']['location'] = $this->location;
        $params['transaction']['partnerId'] = $this->partnerId;
        $params['transaction']['terminal'] = $this->terminal;
        $params['origLocation'] = $this->location;
        $params['origPartnerId'] = $this->partnerId;
        $params['origTerminal'] = $this->terminal;

        return $soap->__soapCall('refund', [$params]);
    }

    public function batchLoad($params)
    {
        
        $soap = $this->getConnectionPCPoints();
        return $soap->__soapCall('batchLoad', [$params]);
    }

    public function registerOrder($params)
    {
        $soap = $this->getConnectionCftAfl();

        $params['partnerId'] = $this->partnerId;

        return $soap->__soapCall('registerOrder', [$params]);
    }

    public function getOrderInfo($params)
    {
        $soap = $this->getConnectionCftAfl();

        $params['partnerId'] = $this->partnerId;

        return $soap->__soapCall('getOrderInfo', [$params]);
    }
}
