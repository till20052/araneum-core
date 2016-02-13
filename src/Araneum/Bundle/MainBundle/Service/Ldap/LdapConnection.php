<?php
namespace Araneum\Bundle\MainBundle\Service\Ldap;

/**
 * LdapConnection
 * @package Araneum\Bundle\MainBundle\Service\Ldap
 */
class LdapConnection
{
    protected $connection;
    private $host;
    private $port;
    private $version;
    private $useSsl;
    private $useStartTls;
    private $useSasl;
    private $optReferrals;

    /**
     * LdapConnection constructor.
     * @param null $host
     * @param int  $port
     * @param int  $version
     * @param bool $useSsl
     * @param bool $useStartTls
     * @param bool $useSasl
     * @param bool $optReferrals
     * @throws \Exception
     */
    public function __construct($host = null, $port = 389, $version = 3, $useSsl = false, $useStartTls = false, $useSasl = false, $optReferrals = false)
    {
        if (!extension_loaded('ldap')) {
            throw new \Exception('The ldap module is needed.');
        }

        $this->host = $host;
        $this->port = $port;
        $this->version = $version;
        $this->useSsl = (bool) $useSsl;
        $this->useStartTls = (bool) $useStartTls;
        $this->useSasl = (bool) $useSasl;
        $this->optReferrals = (bool) $optReferrals;
    }

    /**
     * destruct
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Close connected
     */
    public function disconnect()
    {
        if ($this->connection && is_resource($this->connection)) {
            ldap_unbind($this->connection);
        }

        $this->connection = null;
    }

    /**
     * Access to ldap connection.
     * @param string $dn
     * @param string $password
     * @throws \Exception
     */
    public function bind($dn = null, $password = null)
    {
        if (!$this->connection) {
            $this->connect();
        }

        if (!$this->useSasl) {
            if (false === @ldap_bind($this->connection, $dn, $password)) {
                throw new \Exception(ldap_error($this->connection));
            }
        } else {
            if (false === @ldap_sasl_bind($this->connection, $dn, $password)) {
                throw new \Exception(ldap_error($this->connection));
            }
        }

    }

    /**
     * Conncetd to LDAP
     */
    private function connect()
    {
        if (!$this->connection) {
            $host = $this->host;

            if ($this->useSsl) {
                $host = 'ldaps://'.$host;
            }

            $this->connection = ldap_connect($host, $this->port);
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, $this->version);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, $this->optReferrals);

            if ($this->useStartTls) {
                ldap_start_tls($this->connection);
            }
        }
    }
}
