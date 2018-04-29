<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Usr;

class UsrRepository extends EntityRepository
{
	public function add($params){
		$em = $this->getEntityManager();
		$u = new Usr();
		$u->setLastName($params['lastName']);
		$u->setFirstName($params['firstName']);
		$u->setPatronymic($params['patronymic']);
		$u->setPhone($params['phone']);
		$u->setEmail($params['email']);
		$em->persist($u);
		try{
			$em->flush();
		}
		catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
			throw $e;
		}
		return true;
	}

	public function deleteById($id){
		$em = $this->getEntityManager();
		$u = $em->getRepository('App\Entity\Usr')->findOneById($id);
		if ($u){
			$em->remove($u);
			$em->flush();
			return true;
		}
		else{
			return false;
		}		
	}

    public function findFiltered($params){
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	$qb->select('u')->
            from('App\Entity\Usr', 'u');
		$i = 1;            
		foreach ($params as $key => $value) {
			$j = $i+1;
        	$qb->andWhere("u.$key = ?$j");
        	// $qb->setParameter($i, $key); If only we could do this... luckily we don't have to sanitize keys anyways
        	$qb->setParameter($j, $value);
        	$i+=2;
		}         
    	return $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function updateById($id, $params){
    	$em = $this->getEntityManager();
		$u = $em->getRepository('App\Entity\Usr')->findOneById($id);
		if ($u){
			if (isset($params['lastName']))
				$u->setLastName($params['lastName']);
			if (isset($params['firstName']))
				$u->setFirstName($params['firstName']);
			if (isset($params['patronymic']))
				$u->setPatronymic($params['patronymic']);
			if (isset($params['phone']))
				$u->setPhone($params['phone']);
			if (isset($params['email']))
				$u->setEmail($params['email']);
			$em->persist($u);
			try{
				$em->flush();
			}
			catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
				throw $e;
			}
			return true;				
		}
		else {
			return false;
		}
    }
}