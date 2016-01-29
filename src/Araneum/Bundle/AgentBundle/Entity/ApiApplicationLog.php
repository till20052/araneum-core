<?php

namespace Araneum\Bundle\AgentBundle\Entity;

use Araneum\Base\EntityTrait\DateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SpotLog
 *
 * @ORM\Table("araneum_api_application_log")
 * @ORM\Entity(repositoryClass="Araneum\Bundle\AgentBundle\Repository\SpotLogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApiApplicationLog
{
    use DateTrait;

    /**
     * Exceptions not found
     */
    const TYPE_OK = 1;

    /**
     * Bad Method Call Exception
     */
    const TYPE_BAD_METHOD_CALL = 2;

    /**
     * Curl Exception
     */
    const TYPE_CURL = 3;

    /**
     * Request Exception
     */
    const TYPE_REQUEST = 4;

    /**
     * All other Exceptions
     */
    const TYPE_OTHER_EXCEPTION = 4;

    /**
     * Error types
     *
     * @var array
     */
    public static $types = [
        self::TYPE_OK => "Exceptions not found",
        self::TYPE_BAD_METHOD_CALL => "Bad Method Call Exception",
        self::TYPE_CURL => "Curl Exception",
        self::TYPE_REQUEST => "Request Exception",
        self::TYPE_OTHER_EXCEPTION => "Exception",
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
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default":1})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="request", type="text", nullable=false, options={"default":"Empty request"})
     */
    private $request;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", nullable=false, options={"default":"Empty response"})
     */
    private $response;

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
     * @return SpotLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param  string $request
     * @return SpotLog
     */
    public function setRequest($request)
    {
        $this->request = $request;

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
     * @return SpotLog
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Return text value of status
     *
     * @param  integer $type
     * @return string
     */
    public function getMessageType($type)
    {
        return self::$types[$type];
    }
}
