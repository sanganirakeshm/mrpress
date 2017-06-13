<?php

namespace Ld\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PermissionCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PermissionCategoryRepository extends EntityRepository
{
	public function getAllActivePermissionCategory() {
	
		$query = $this->createQueryBuilder('pc')
						->where('pc.status =:status')
						->setParameter('status', 1)
						->andWhere('pc.isDeleted =:deleted')
						->setParameter('deleted', 0)
						->orderBy('pc.id', 'DESC');
	
		//return $query->getQuery()->getResult();
		return $query;
	}
	
	public function getPermissionCategoryGridList($limit = 0, $offset = 10, $orderBy = "id", $sortOrder = "asc", $searchData, $SearchType, $objHelper) {
	
		$data = $this->trim_serach_data($searchData, $SearchType);
	
		$query = $this->createQueryBuilder('pc')
						->where('pc.isDeleted =:deleted')
						->setParameter('deleted', 0)
						->orderBy('pc.id', 'DESC');
	
		if ($SearchType == 'ORLIKE') {
	
			$likeStr = $objHelper->orLikeSearch($data);
		}
		if ($SearchType == 'ANDLIKE') {
	
			$likeStr = $objHelper->andLikeSearch($data);
		}
	
		if ($likeStr) {
	
			$query->andWhere($likeStr);
		}
	
		$query->orderBy($orderBy, $sortOrder);
			
		$countData = count($query->getQuery()->getArrayResult());
			
		$query->setMaxResults($limit);
		$query->setFirstResult($offset);
			
		$result = $query->getQuery()->getResult();
	
		$dataResult = array();
			
		if ($countData > 0) {
	
			$dataResult['result'] = $result;
			$dataResult['totalRecord'] = $countData;
	
			return $dataResult;
		}
		return false;
	}
	
	public function trim_serach_data($searchData, $SearchType) {
	
		$QueryStr = array();
	
		if (!empty($searchData)) {
	
			if ($SearchType == 'ANDLIKE') {
	
				$i = 0;
				foreach ($searchData as $key => $val) {
	
					if ($key == 'name' && !empty($val)) {
	
						$QueryStr[$i]['Field'] = 'pc.name';
						$QueryStr[$i]['Value'] = $val;
						$QueryStr[$i]['Operator'] = 'LIKE';
					}
	
					$i++;
				}
			} else {
	
			}
		}
		return $QueryStr;
	}
}