<?php
namespace Araneum\Bundle\MainBundle\Service\Ldap;

use FOS\UserBundle\Model\UserManager as FOSUserManager;
use FR3D\LdapBundle\Ldap\LdapManager;
use FR3D\LdapBundle\Driver\LdapDriverInterface;
use FR3D\LdapBundle\Model\LdapUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * LdapUseManager
 * @package Araneum\Bundle\MainBundle\Service\Ldap
 */
class LdapUseManager extends LdapManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * LdapUseManager constructor.
     *
     * @param ContainerInterface $container
     * @param EncoderFactoryInterface $encoderFactory
     * @param LdapDriverInterface $driver
     * @param $userManager
     * @param array $params
     */
    public function __construct(
        ContainerInterface $container,
        EncoderFactoryInterface $encoderFactory,
        LdapDriverInterface $driver,
        $userManager,
        array $params)
    {
        parent::__construct($driver, $userManager, $params);
        $this->container = $container;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Hydrates an user entity with ldap attributes.
     *
     * @param  UserInterface $user  user to hydrate
     * @param  array         $entry ldap result
     *
     * @return UserInterface
     */
    protected function hydrate(UserInterface $user, array $entry)
    {
        $params = $this->container->getParameter('ldap');
        if (isset($params['default_user_roles'])) {
            if (is_string($params['default_user_roles'])) {
                $user->setRole($params['default_user_roles']);
            } elseif (is_array($params['default_user_roles'])) {
                $user->setRoles($params['default_user_roles']);
            }
        }

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        foreach ($this->params['attributes'] as $attr) {
            if (!array_key_exists($attr['ldap_attr'], $entry)) {
                continue;
            }

            $ldapValue = $entry[$attr['ldap_attr']];
            $value = null;

            if (!array_key_exists('count', $ldapValue) ||  $ldapValue['count'] == 1) {
                $value = $ldapValue[0];
            } else {
                $value = array_slice($ldapValue, 1);
            }

            call_user_func(array($user, $attr['user_method']), $value);
        }

        if ($user instanceof LdapUserInterface) {
            $user->setDn($entry['dn']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function bind(UserInterface $user, $password)
    {
        $bind = parent::bind($user, $password);
        if ($bind) {
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);

            $user->setPassword($encoded);
        }

        return $bind;
    }

}
