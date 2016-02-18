<?php

namespace Araneum\Bundle\MainBundle\Service;

use Araneum\Bundle\AgentBundle\Entity\Error;
use Araneum\Bundle\AgentBundle\Repository\CustomerRepository;
use Araneum\Bundle\AgentBundle\Repository\ErrorRepository;
use Araneum\Bundle\AgentBundle\Repository\LeadRepository;
use Araneum\Bundle\MailBundle\Repository\MailRepository;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\AgentBundle\Repository\ApplicationLogRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Araneum\Bundle\MainBundle\Repository\RunnerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Araneum\Bundle\UserBundle\Entity\User;

/**
 * Class StatisticsService
 *
 * @package Araneum\Bundle\MainBundle\Service
 */
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
        $this->hours = $this->createTimeRange(date('Y-m-d H:s', time() - 86400), date('Y-m-d H:s', time()));
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
    public function getApplicationsStatusesDaily()
    {
        return $this->getApplicationRepository()->getApplicationStatusesDaily();
    }

    /**
     * Get average statuses Daily
     *
     * @return array
     */
    public function getAverageApplicationStatusesDaily()
    {
        return $this->getApplicationLogRepository()->getAverageApplicationStatusesDaily();
    }

    /**
     *  Get array
     *
     * @param  array $pack
     * @param  mixed $column
     * @return array
     */
    public function getResultByColumnName(array $pack, $column)
    {
        return array_values(array_column($pack, $column));
    }

    /**
     * Prepare data for Daily application statuses chart
     *
     * @return array
     */
    public function prepareResulForDailyApplications()
    {
        $statusesDaily = $this->getApplicationsStatusesDaily();

        return
            [
                'applications' => $this->getResultByColumnName($statusesDaily, 'name'),
                'errors' => $this->getResultByColumnName($statusesDaily, 'errors'),
                'problems' => $this->getResultByColumnName($statusesDaily, 'problems'),
                'success' => $this->getResultByColumnName($statusesDaily, 'success'),
                'disabled' => $this->getResultByColumnName($statusesDaily, 'disabled'),
            ];
    }

    /**
     * Prepare data for Daily application average chart
     *
     * @return array
     */
    public function prepareResultForDailyAverageStatuses()
    {
        $statusesDailyAverage = $this->getAverageApplicationStatusesDaily();

        return [
            'success' => $this->getStatusesByPeriod($statusesDailyAverage, 'success'),
            'problems' => $this->getStatusesByPeriod($statusesDailyAverage, 'problems'),
            'errors' => $this->getStatusesByPeriod($statusesDailyAverage, 'errors'),
            'disabled' => $this->getStatusesByPeriod($statusesDailyAverage, 'disabled'),
        ];
    }

    /**
     * Prepare data for Cluster average load chart
     *
     * @return array
     */
    public function getResultForClusterAverage()
    {
        return $this->getChartStructure($this->getClusterLoadAverage(), 'apt');
    }

    /**
     * Prepare result for cluster Up time
     *
     * @return array
     */
    public function getResultForClusterUpTime()
    {
        $upTime = $this->getClusterRepository()->getClusterUpTime();

        return $this->prepareResultForUpTime($upTime, ['success', 'problem', 'offline']);
    }

    /**
     * Get Summary statistics
     *
     * @return array
     */
    public function getSummary()
    {
        return [
            'applications' => $this->entityManager
                ->getRepository('AraneumMainBundle:Application')
                ->count(),
            'clusters' => $this->entityManager
                ->getRepository('AraneumMainBundle:Cluster')
                ->count(),
            'admins' => $this->entityManager
                ->getRepository('AraneumUserBundle:User')
                ->count(),
            'connections' => $this->entityManager
                ->getRepository('AraneumMainBundle:Connection')
                ->count(),
            'locales' => $this->entityManager
                ->getRepository('AraneumMainBundle:Locale')
                ->count(),
        ];
    }

    /**
     * Get registered Leads from all Applications in last 24 hours
     *
     * @return array
     */
    public function getRegisteredLeadsFromAppsInLast24H()
    {
        /** @var LeadRepository $repository */
        $repository = $this->entityManager->getRepository('AraneumAgentBundle:Lead');

        return [
            'count' => $repository->countByTimeInterval(),
            'data' => $this->getChartStructure(
                $repository->getRegisteredLeadsFromAppsInLast24H(),
                'leadsCount'
            ),
        ];
    }


    /**
     * Get UpTime from all Runners in last 24 hours
     *
     * @return array
     */
    public function getResultsForRunnersUpTime()
    {
        $upTime = $this->getRunnerRepository()->getRunnersUpTime();

        return $this->prepareResultForUpTime($upTime, ['success', 'appProblem', 'problem', 'offline']);
    }

    /**
     * Get Average for all Runners in last 24 hours
     *
     * @return array
     */
    public function getResultsForRunnersAverage()
    {
        return $this->getChartStructure(
            $this->getRunnerRepository()->getRunnerLoadAverage(),
            'apt'
        );
    }

    /**
     * Get received Errors from all Applications in last 24 hours
     *
     * @return array
     */
    public function getReceivedErrorsFromAppsInLast24H()
    {
        /** @var ErrorRepository $repository */
        $repository = $this->entityManager->getRepository('AraneumAgentBundle:Error');

        return [
            'count' => $repository->countErrorsByTimeInterval(),
            'data' => $repository->getReceivedErrorsFromAppsInLast24H(),
        ];
    }

    /**
     * Get Registered Customers
     *
     * @return array
     */
    public function getRegisteredCustomersFromApplications()
    {
        /**
         * @var CustomerRepository $repository
         */
        $repository = $this->entityManager->getRepository('AraneumAgentBundle:Customer');

        return [
            'count' => $repository->countByTimeInterval(),
            'data' => $this->getChartStructure($repository->getRegisteredCustomersFromApplications(), 'customers'),
        ];
    }

    /**
     * Get Received Emails
     *
     * @return array
     */
    public function getReceivedEmailsFromApplications()
    {
        /**
         * @var MailRepository $repository
         */
        $repository = $this->entityManager->getRepository('AraneumMailBundle:Mail');

        return [
            'count' => $repository->countByTimeInterval(),
            'data' => $this->getChartStructure($repository->getReceivedEmailsFromApplications(), 'emails'),
        ];
    }

    /**
     * Init chart structure
     *
     * @param  array  $list
     * @param  string $countField
     * @return array
     */
    private function getChartStructure(array $list, $countField = 'cnt')
    {
        $data = [];

        foreach ($list as $item) {
            if (!isset($data[$item['name']])) {
                $data[$item['name']] = [
                    'label' => $item['name'],
                    'data' => $this->hours,
                ];
            }

            $key = $item['hours'];
            if ($key < 10) {
                $key = '0'.$key;
            }

            $data[$item['name']]['data'][$key] = round($item[$countField]);
        }

        $data = array_values($data);

        foreach ($data as &$item) {
            $token = [];
            foreach ($item['data'] as $key => $val) {
                $token[] = [
                    $key,
                    $val,
                ];
            }
            $item['data'] = $token;
        }

        return array_values($data);
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
     * Get errors by Application by hour
     *
     * @param  array  $pack
     * @param  string $status
     * @param  string $period
     * @return array
     */
    private function getStatusesByPeriod(array $pack, $status, $period = 'hours')
    {
        $prepareArray = $this->hours;
        $resultArray = [];

        foreach ($pack as $item) {
            if (isset($item[$status])) {

                if ($item[$period] <= 9) {
                    $item[$period] = "0".$item[$period];
                }

                $prepareArray[$item[$period]] = $item[$status];
            }
        }

        foreach ($prepareArray as $key => $value) {
            $resultArray[] = [
                (string) $key,
                $value,
            ];
        }

        return $resultArray;
    }

    /**
     * Create time range
     *
     * @param  mixed $start start time, e.g., 9:30am or 9:30
     * @param  mixed $end   end time, e.g., 5:30pm or 17:30
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
     * Prepare result for cluster Up time
     * @param array $dataArray
     * @param array $statuses
     * @return array
     */
    private function prepareResultForUpTime($dataArray, $statuses)
    {
        $chartArray = [];
        $result = [];
        foreach ($statuses as $status) {
            $chartArray[$status] = [
                'label' => $status,
                'data' => [],
            ];
        }
        foreach ($dataArray as $array) {
            foreach ($statuses as $status) {
                array_push($chartArray[$status]['data'], [
                    $array['name'],
                    $array[$status],
                ]);
            }
        }
        foreach ($chartArray as $array) {
            array_push($result, $array);
        }

        return $result;
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
     * Get application repositoty
     *
     * @return ApplicationRepository
     */
    private function getApplicationRepository()
    {
        return $this->entityManager->getRepository('AraneumMainBundle:Application');
    }

    /**
     * Get runner repositoty
     *
     * @return RunnerRepository
     */
    private function getRunnerRepository()
    {
        return $this->entityManager->getRepository('AraneumMainBundle:Runner');
    }
}
