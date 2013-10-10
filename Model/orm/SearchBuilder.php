<?php

namespace Ibrows\SimpleSearchBundle\Model\orm;

use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Ibrows\SimpleSearchBundle\Model\BaseSearchBuilder;

class SearchBuilder extends BaseSearchBuilder
{
    protected $registry;
    
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
    
    public function getResult(FormInterface $form, $className, $hydrationMode = null)
    {
        $qb = $this->getQueryBuilder($form, $className);
        return $qb->getQuery()->getResult($hydrationMode);
    }
    
    /**
     * @param FormInterface $form
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder(FormInterface $form, $className, $alias = 'o')
    {
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->registry->getManagerForClass($className)->createQueryBuilder();
        $qb
            ->select($alias)
            ->from($className, $alias)
        ;
        
        return $this->buildQueryRecursive($form, $qb, $className, $alias);
    }
    
    public function buildQueryRecursive(FormInterface $form, QueryBuilder $qb, $className, $alias = 'o')
    {
        $qb = $this->buildQuery($form, $qb, $className, $alias);
        
        foreach($form->all() as $child) {
            $qb = $this->buildQuery($child, $qb, $className, $alias);
        }
        return $qb;
    }
    
    public function buildQuery(FormInterface $form, QueryBuilder $qb, $className, $alias = 'o')
    {
        $searchClass = $this->getSearchClass($form);
        if (!$searchClass || $searchClass !== $className || $this->hasEmtpyData($form)) {
            return $qb;
        }
        
        $searchMethod = $this->getSearchMethod($form);
        if ($searchMethod) {
            $repo = $this->registry->getManagerForClass($searchClass)->getRepository($searchClass);
            $repo->$searchMethod($qb, $alias, $form, $this);
            return $qb;
        }
        
        $searchField = $this->getSearchField($form);
        $searchCondition = $this->getSearchCondition($form);
        $searchValue = $this->getSearchValue($form);
        $variable = $alias.'_'.$form->getName().'_value';
        
        if ($searchCondition == 'IN' && $searchValue instanceof Collection) {
            //TODO: find a better solution to transform collection
            $ids = array();
            foreach($searchValue as $obj) {
                $ids[] = $obj->getId();
            }
            
            if (count($ids) > 0) {
                $qb->andWhere("{$alias}.{$searchField} $searchCondition (:{$variable})")->setParameter($variable, $ids);
            } else {
                $qb->andWhere("1 = 2");
            }
        } elseif ($searchValue) {
            $qb->andWhere("{$alias}.{$searchField} $searchCondition :{$variable}")->setParameter($variable, $searchValue);
        }
        
        return $qb;
    }
}
