<?php

namespace GenericBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use \Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    public function findByRoles(array $roles)
    {

        $users = array();
        foreach($roles as $role)
        {
            $users = array_merge($users,$this->findByRole($role));
        }
        return $users;

    }

    public function getUserofTier($tier)
    {
        $users = array();
        $em = $this->getEntityManager();
        $users = array_merge($users,$em->getRepository('GenericBundle:User')->findBy(array('tier'=>$tier )));

        $etablis = $em->getRepository('GenericBundle:Etablissement')->findBy(array('tier'=>$tier ));

        foreach( $etablis as $etab){
            $users = array_merge($users,$em->getRepository('GenericBundle:User')->findBy(array('etablissement'=>$etab )));
        }
        return $users;
    }
}
