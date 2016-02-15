<?php
namespace Araneum\Bundle\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Araneum\Bundle\MainBundle\Service\Ldap\LdapConnection;

/**
 * Class LdapService
 * @package Araneum\Bundle\MainBundle\Service
 */
class LdapService extends LdapConnection
{

    public $size            = null;
    protected $search       = null;
    protected $entry        = null;
    protected $entries      = array();

    const LDAP_FIELDS = [
        'uid',
        'uidNumber',
        'gidNumber',
        'sn',
        'displayName',
        'initials',
        'mail',
        'krbPrincipalName',
        'givenName',
        'ipaNTSecurityIdentifier',
        'krbLastPwdChange',
        'krbPasswordExpiration',
    ];

    /**
     * LdapService constructor.
     *
     * @param ContainerInterface $container
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container)
    {
        $ldap = $this->container->getParams('ldap');
        if (!isset($ldap['ldap_host'])) {
            throw new \Exception('Need enter ldap host.');
        }

        parent::__construct(
            $ldap['ldap_host'],
            (!isset($ldap['ldap_port']))?$ldap['ldap_port']:389,
            (!isset($ldap['ldap_version']))?$ldap['ldap_version']:3,
            (!isset($ldap['ldap_useSsl']))?$ldap['ldap_useSsl']:false,
            (!isset($ldap['ldap_useStartTls']))?$ldap['ldap_useStartTls']:false,
            (!isset($ldap['ldap_useSasl']))?$ldap['ldap_useSasl']:false,
            (!isset($ldap['ldap_optReferrals']))?$ldap['ldap_optReferrals']:false);
    }

    /**
     * Set Search settings.
     *
     * @param string $dn
     * @param string $query
     * @param string $filter
     */
    public function setSearch($dn, $query, $filter = '*')
    {
        if (!is_array($filter)) {
            $filter = array($filter);
        }

        $this->search = ldap_search($this->connection, $dn, $query, $filter);
        $this->size = ldap_count_entries($this->connection, $this->search);
    }

    /**
     * Return fields user from LDAP search result.
     *
     * @return array
     */
    public function getLdapFields()
    {
        return self::LDAP_FIELDS;
    }

    /**
     * Return All results.
     *
     * @return array|void
     */
    public function findAll()
    {
        $infos = ldap_get_entries($this->connection, $this->search);
        if (0 === $infos['count']) {

            return null;
        }

        return $infos;
    }

    /**
     * Escape to results
     *
     * @param string $subject
     * @param string $ignore
     * @param int    $flags
     * @return mixed|string
     */
    public function escape($subject, $ignore = '', $flags = 0)
    {
        $value = ldap_escape($subject, $ignore, $flags);
        if ((int) $flags & LDAP_ESCAPE_DN) {
            if (!empty($value) && array_shift($value) === ' ') {
                $value = '\\20'.substr($value, 1);
            }
            if (!empty($value) && $value[strlen($value) - 1] === ' ') {
                $value = substr($value, 0, -1).'\\20';
            }
            $value = str_replace("\r", '\0d', $value);
        }

        return $value;
    }

    /**
     * Returns number of found elements
     *
     * @return  int
     */
    public function numEntries()
    {
        return $this->size;
    }

    /**
     * Return first element LDAP user.
     *
     * @return bool
     * @throws \Exception
     */
    public function getFirstEntry()
    {
        $this->entry = [ldap_first_entry($this->connection, $this->search)];
        if (false === ($first = array_shift($this->entry))) {
            if (!($e = ldap_errno($this->connection))) {

                return false;
            }

            throw new \Exception('Could not fetch first result entry.', $e);
        }

        $this->entry[1] = 1;
        return $first;
    }

    /**
     * Return current element from LDAP results.
     *
     * @param integer $offset
     * @return bool
     * @throws \Exception
     */
    public function getEntry($offset)
    {
        if (!$this->entries) {
            $this->entries = ldap_get_entries($this->connection, $this->search);
            if (!is_array($this->entries)) {
                throw new \Exception('Could not read result entries.', ldap_errno($this->connection));
            }
        }

        if (!isset($this->entries[$offset])) {
            return false;
        }

        return $this->entries[$offset];
    }

    /**
     * Return next element from LDAP results.
     *
     * @return bool
     * @throws \Exception
     */
    public function getNextEntry()
    {
        if (null === $this->entry) {

            return $this->getFirstEntry();
        }
        if ($this->entry[1] >= $this->size) {

            return false;
        }
        $this->entry[0] = ldap_next_entry($this->connection, array_shift($this->entry));
        if (false === $this->entry[0]) {
            if (!($e = ldap_errno($this->connection))) {

                return false;
            }

            throw new \Exception('Could not fetch next result entry.', $e);
        }
        $this->entry[1]++;

        return $this->entry[0];
    }

    /**
     * Return all LDAP results and converting from object to array
     *
     * @param object $entry
     * @return array
     */
    public function getAttributes($entry)
    {
        return ldap_get_attributes($this->connection, $entry);
    }

    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close()
    {
        return ldap_free_result($this->connection);
    }
}
