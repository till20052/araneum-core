<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ApplicationRepository
     */
    private $repository;

    /**
     * @var array $hours
     */
    private $hours;

    /**
     * StatisticsService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository('AraneumMainBundle:Application');
        $this->hours = $this->create_time_range(date('Y-m-d H:s', time()-86400), date('Y-m-d H:s', time()), '1 hour');
    }

    /**
     * Get statistics of all applications by next conditions:
     *  - online
     *  - has problems
     *  - has errors
     *  - disabled
     *
     * @return \stdClass
     */
    public function getApplicationsStatistics()
    {
        return $this->repository->getApplicationsStatistics();
    }

    /**
     * Get statistics of all applications by statuses last 24 hours
     *
     * -error
     * -problem
     * -ok
     * -disabled
     */
    public function getApplicationsStatusesDayly()
    {

        return $this->repository->getApplicationStatusesDayly();
    }

    /**
     * Get average statuses dayly
     *
     * @return array
     */
    public function getAverageApplicationStatusesDayly()
    {
        $repository = $this->entityManager->getRepository('AraneumAgentBundle:ApplicationLog');

        return $repository->getAverageApplicationStatusesDayly();
    }

    /**
     * Get applications array
     *
     * @param array $pack
     * @return array
     */
    public function getApplications(array $pack){
        $array = array_values(array_column($pack, 'name'));

        return $array;
    }

    /**
     * Get errors array
     *
     * @param array $pack
     * @return array
     */
    public function getErrors(array $pack){
        $array = array_values(array_column($pack, 'errors'));

        return $array;
    }

    /**
     * Get problems array
     *
     * @param array $pack
     * @return array
     */
    public function getProblems(array $pack){
        $array = array_values(array_column($pack, 'problems'));

        return $array;
    }

    /**
     * Get OK array
     *
     * @param array $pack
     * @return array
     */
    public function getSuccess(array $pack){
        $array = array_values(array_column($pack, 'success'));

        return $array;
    }

    /**
     * Get Disabled array
     *
     * @param array $pack
     * @return array
     */
    public function getDisabled(array $pack){
        $array = array_values(array_column($pack, 'disabled'));

        return $array;
    }

    /**
     * Get errors by Application by hour
     *
     * @param array $pack
     * @return array
     */
    public function getErrorsByHours(array $pack){
        $array = $this->hours;

        foreach($pack as $item){
            if(isset($item['errors'])) {
                $array[$item['hours']] = $item['errors'];
            }
        }

        return $array;
    }


    /**
     * Get problems by Application by hour
     *
     * @param array $pack
     * @return array
     */
    public function getProblemsByHours(array $pack){
        $array = $this->hours;

        foreach($pack as $item){
            if(isset($item['problems'])) {
                $array[$item['hours']] = $item['problems'];
            }
        }

        return $array;
    }


    /**
     * Get Success by Application by hour
     *
     * @param array $pack
     * @return array
     */
    public function getSuccessByHours(array $pack){
        $array = $this->hours;

        foreach($pack as $item){
           if(isset($item['success'])) {
               $array[$item['hours']] = $item['success'];
           }
        }

        return $array;
    }


    /**
     * Get disabled by Application by hour
     *
     * @param array $pack
     * @return array
     */
    public function getDisabledByHours(array $pack){
        $array = $this->hours;

        foreach($pack as $item){
            if(isset($item['disabled'])) {
                $array[$item['hours']] = $item['disabled'];
            }
        }

        return $array;
    }


    /**
     * create_time_range
     *
     * @param mixed $start start time, e.g., 9:30am or 9:30
     * @param mixed $end   end time, e.g., 5:30pm or 17:30
     * @param string $by   1 hour, 1 mins, 1 secs, etc.
     * @access public
     * @return array
     */
    function create_time_range($start, $end, $by='30 mins') {

        $start_time = strtotime($start);
        $end_time   = strtotime($end);

        $current    = time();
        $add_time   = strtotime('+'.$by, $current);
        $diff       = $add_time-$current;

        $times = array();
        $i=0;

        while ($start_time < $end_time) {
            $times[date('H', $start_time)] = $i;
            $start_time += $diff;
        }

        return $times;
    }

}