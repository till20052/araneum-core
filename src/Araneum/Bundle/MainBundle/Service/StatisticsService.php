<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\AgentBundle\Repository\ApplicationLogRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Araneum\Bundle\AgentBundle\Repository\ClusterLogRepository;

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

    const COLORS = [
        '#5d9cec',
        '#27c24c',
        '#23b7e5',
        '#ff902b',
        '#f05050',
        '#37bc9b',
        '#f532e5',
        '#7266ba',
        '#fad732',
        '#dde6e9'
    ];

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
    private function getApplicationRepository()
    {
        return $this->entityManager->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Get application log repository
     *
     * @return ApplicationLogRepository
     */
    private function getApplicationLogRepository()
    {
        return $this->entityManager->getRepository('AraneumAgentBundle:ApplicationLog');
    }

    /**
     * Get Cluster repository
     *
     * @return ClusterRepository
     */
    private function getClusterRepository()
    {
        return $this->entityManager->getRepository('AraneumMainBundle:Cluster');
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
     * Get averade cluster load data
     *
     * @return array
     */
    private function getClusterLoadAverage()
    {
        return $this->getClusterRepository()->getClusterLoadAverage();
    }

    /**
     *
     * Get data from repository
     */
    private function getClusterUpTime()
    {
        return $this->getClusterRepository()->getClusterUpTime();
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
    private function getStatusesByPeriod(array $pack, $status, $period = 'hours')
    {
        $prepareArray = $this->hours;
        $resultArray = [];

        foreach ($pack as $item) {
            if (isset($item[$status])) {

                if ($item[$period] <= 9) {
                    $item[$period] = "0" . $item[$period];
                }

                $prepareArray[$item[$period]] = $item[$status];
            }
        }

        foreach ($prepareArray as $key => $value) {
            $resultArray[] = [(string)$key, $value];
        }


        return $resultArray;
    }

    /**
     * Prepare data for dayly application statuses chart
     *
     * @return array
     */
    public function prepareResulForDaylyApplications()
    {
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
    public function prepareResultForDaylyAverageStatuses()
    {
        $statusesDaylyAverage = $this->getAverageApplicationStatusesDayly();

        return [
            'success' => $this->getStatusesByPeriod($statusesDaylyAverage, 'success'),
            'problems' => $this->getStatusesByPeriod($statusesDaylyAverage, 'problems'),
            'errors' => $this->getStatusesByPeriod($statusesDaylyAverage, 'errors'),
            'disabled' => $this->getStatusesByPeriod($statusesDaylyAverage, 'disabled')
        ];
    }

    /**
     * Prepare data for Cluster average load chart
     *
     * @return array
     */
    public function prepareResultForClusterAverage()
    {
        $clusterLoadAverage = $this->getClusterLoadAverage();

        $resultArray = [];
        $currentCluster = [
            'label' => '',
            'color' => $this->getColor(),
            'data' => $this->hours
        ];

        $name = null;

        foreach ($clusterLoadAverage as $cluster) {
            if ($currentCluster['label'] != $cluster['name']) {

                if (!is_null($name)) {
                    array_push($resultArray, $currentCluster);
                }

                $name = $cluster['name'];
                $currentCluster = [
                    'label' => $name,
                    'color' => $this->getColor(),
                    'data' => $this->hours
                ];

                if (!is_null($cluster['hours'])) {

                    if ($cluster['hours'] <= 9) {
                        $cluster['hours'] = "0" . $cluster['hours'];
                    }

                    $currentCluster['data'][$cluster['hours']] = round($cluster['apt']);
                }

            } else {
                if (!is_null($cluster['hours'])) {

                    if ($cluster['hours'] <= 9) {
                        $cluster['hours'] = "0" . $cluster['hours'];
                    }

                    $currentCluster['data'][$cluster['hours']] = round($cluster['apt']);
                }
            }
        }

        array_push($resultArray, $currentCluster);

        foreach ($resultArray as &$array) {
            $data = $array['data'];
            $array['data'] = [];
            foreach ($data as $key => $value) {
                array_push($array['data'], [(string)$key, $value]);
            }
        }

        return $resultArray;
    }

    /**
     * Prepare result for cluster Up time
     *
     * @return array
     */
    public function prepareResultForClusterUpTime()
    {
        $clusterUpTime = $this->getClusterUpTime();

        $success = [
            'label' => 'Success',
            'color' => '#27c24c',
            'data' => []
        ];
        $incorrectApplication = [
            'label' => 'Incorrect application',
            'color' => '#ff902b',
            'data' => []
        ];
        $slowConnection = [
            'label' => 'Slow connection',
            'color' => '#fad732',
            'data' => []
        ];
        $unstableConnection = [
            'label' => 'Unstable connection',
            'color' => '#ff902b',
            'data' => []
        ];
        $offline = [
            'label' => 'Offline',
            'color' => '#dde6e9',
            'data' => []
        ];

        foreach($clusterUpTime as $array){
            array_push($success['data'], [$array['name'], $array['success']]);
            array_push($incorrectApplication['data'], [$array['name'], $array['incorrect_application']]);
            array_push($slowConnection['data'], [$array['name'], $array['slow_connection']]);
            array_push($unstableConnection['data'], [$array['name'], $array['unstable_connection']]);
            array_push($offline['data'], [$array['name'], $array['offline']]);
        }

        return [$success, $incorrectApplication, $slowConnection, $unstableConnection, $offline];
    }

    /**
     * Create time range
     *
     * @param mixed $start start time, e.g., 9:30am or 9:30
     * @param mixed $end end time, e.g., 5:30pm or 17:30
     * @access public
     * @return array
     */
    private function createTimeRange($start, $end)
    {
        $times = [];

        $begin = new \DateTime($start);
        $end = new \DateTime($end);

        $interval = new \DateInterval('PT1H');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        foreach ($dateRange as $date) {
            $times[$date->format('H')] = 0;
        }

        return $times;
    }

    /**
     * Get color
     *
     * @return mixed
     */
    private function getColor()
    {
        static $index = -1;

        if ($index + 1 >= count($this::COLORS)) {
            $index = -1;
        }

        return $this::COLORS[++$index];
    }
}