<?php

namespace Araneum\Bundle\MailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Araneum\Bundle\MailBundle\Entity\MailLog as MailLog;

/**
 * MailLogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MailLogRepository extends EntityRepository
{
    /**
     * Set Mail log status array( '1' => 'OK', '2' => 'ERROR' )
     * @param array $params
     * @throws \Exception
     */
    public function setMailLog(array $params) {
        $manager = $this->getEntityManager();

        try {
            $eMailLog = new MailLog();
            $eMailLog->setStatus($params['status']);
            $eMailLog->setMailId($params['mail_id']);

            $manager->persist($eMailLog);
            $manager->flush();
        } catch(\Exception $e) {
            throw new \Exception('Dont save in MailLog.');
        }
    }
}
