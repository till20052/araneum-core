<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CustomerLog
 *
 * @ORM\Table("araneum_customers_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\CustomersLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CustomerLog
{
    use DateTrait;

    const STATUS_OK    = 0;
    const STATUS_ERROR = 100;

    const ACTION_RESET_PASSWORD = 'reset_password';
    const ACTION_LOGIN = 'login';
    const ACTION_CREATE = 'create';

    private static $statuses = [
        self::STATUS_OK => 'ok',
        self::STATUS_ERROR => 'error',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\MainBundle\Entity\Application")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    private $application;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Araneum\Bundle\AgentBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private $response;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * Get list of Customer statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return self::$statuses;
    }

    /**
     * Get Customer status description
     *
     * @param  integer $status
     * @return string
     */
    public static function getStatusDescription($status)
    {
        if (!isset(self::$statuses[$status])) {
            return '[undefined]';
        }

        return self::$statuses[$status];
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set application
     *
     * @param  Application $application
     * @return CustomerLog
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get Customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customer
     *
     * @param  Customer $customer
     * @return CustomerLog
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get Action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param  string $action
     * @return CustomerLog
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param  string $response
     * @return CustomerLog
     */
    public function setResponse($response)
    {
        if (!is_string($response)) {
            $response = json_encode($response);
        }

        $this->response = $response;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param  int $status
     * @return CustomerLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
