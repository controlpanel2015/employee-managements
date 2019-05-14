<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    const GENDER_MAP = ['M' => "Male", "F" => "Female", "O" => "Others"];

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getGenderWiseEmployee()
    {
        $data = $this
            ->createQueryBuilder('e')
            ->select("count(e.id) as no_of_employee, e.gender")
            ->groupBy("e.gender")
            ->getQuery()
            ->getResult();

        $report = [];

        foreach ($data as $row) {

            $report[] = [
                'gender' => self::GENDER_MAP[$row['gender']],
                'no_of_employee' => $row['no_of_employee'],
            ];
        }

        return $report;
    }

    public function getQueryBySearchCriteria(array $searchCriteria)
    {
        $qb = $this
            ->createQueryBuilder('e');

        if(isset($searchCriteria['name']) && !empty(trim($searchCriteria['name']))) {
            $qb
                ->where($qb->expr()->like('e.name', ':name'))
                ->setParameter('name', $searchCriteria['name']);
            ;
        }

        if(isset($searchCriteria['age']) && !empty(trim($searchCriteria['age']))) {
            $qb
                ->where("e.date_of_birth = DATE_ADD(CURRENT_DATE(),:age, 'year')")
                ->setParameter(age,$searchCriteria['age']);
        }

        return $qb->getQuery();
    }
}

