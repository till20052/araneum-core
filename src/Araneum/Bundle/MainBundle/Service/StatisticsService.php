<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\AgentBundle\Repository\ApplicationLogRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticsService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

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
        $this->hours = $this->createTimeRange(date('Y-m-d H:s', time() - 86400), date('Y-m-d H:s', time()), '1 hour');
    }

    /**
     * Get application repositoty
     *
     * @return ApplicationRepository
     */
    private function getApplicationRepository(){
        return $this->entityManager->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Get application log repository
     *
     * @return ApplicationLogRepository
     */
    private function getApplicationLogRepository(){
        return $this->entityManager->getRepository('AraneumAgentBundle:ApplicationLog');
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
        return $this->getApplicationRepository()->getApplicationsStatistics();
    }

    /**
     * Get statistics of all applications by statuses last 24 hours
     *
     * -error
     * -problem
     * -ok
     * -disabled
     *
     * @return array
     */
    public function getApplicationsStatusesDayly()
    {
        return $this->getApplicationRepository()->getApplicationStatusesDayly();
    }

    /**
     * Get average statuses dayly
     *
     * @return array
     */
    public function getAverageApplicationStatusesDayly()
    {
        return $this->getApplicationLogRepository()->getAverageApplicationStatusesDayly();
    }

    /**
     * Get array
     *
     * @param array $pack
     * @return array
     */
    public function getResultByColumnName(array $pack, $column)
    {
        return array_values(array_column($pack, $column));
    }

    /**
     * Get errors by Application by hour
     *
     * @param array $pack
     * @return array
     */
    private function getStatusesByPeriod(array $pack, $status, $period='hours')
    {
        $prepareArray = $this->hours;
        $resultArray = [];

        foreach ($pack as $item) {
            if (isset($item[$status])) {
                $prepareArray[$item[$period]] = $item[$status];
            }
        }

        foreach($prepareArray as $key=>$value){
            $resultArray[]=[(string)$key,$value];
        }


        return $resultArray;
    }

    /**
     * Prepare data for dayly application statuses chart
     *
     * @return array
     */
    public function prepareResulForDaylyApplications(){
        $statusesDayly = $this->getApplicationsStatusesDayly();

        return
        [
            'applications' => $this->getResultByColumnName($statusesDayly, 'name'),
            'errors' => $this->getResultByColumnName($statusesDayly, 'errors'),
            'problems' => $this->getResultByColumnName($statusesDayly, 'problems'),
            'success' => $this->getResultByColumnName($statusesDayly, 'success'),
            'disabled' => $this->getResultByColumnName($statusesDayly, 'disabled')
        ];
    }

    /**
     * Prepare data for dayly application average chart
     *
     * @return array
     */
    public function prepareResultForDaylyAverageStatuses(){
        $statusesDaylyAverage = $this->getAverageApplicationStatusesDayly();

        return [
            'errors' => $this->getStatusesByPeriod($statusesDaylyAverage, 'errors'),
            'problems' => $this->getStatusesByPeriod($statusesDaylyAverage, 'problems'),
            'success' => $this->getStatusesByPeriod($statusesDaylyAverage, 'success'),
            'disabled' => $this->getStatusesByPeriod($statusesDaylyAverage, 'disabled')
        ];
    }


    /**
     * create_time_range
     *
     * @param mixed $start start time, e.g., 9:30am or 9:30
     * @param mixed $end end time, e.g., 5:30pm or 17:30
     * @access public
     * @return array
     */
    private function createTimeRange($start, $end)
    {
        $times = [];

        $begin =new \DateTime($start);
        $end = new \DateTime($end);

        $interval = new \DateInterval('PT1H');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        foreach($dateRange as $date){
            $times[$date->format('H')] = 0;
        }

        return $times;
    }

}