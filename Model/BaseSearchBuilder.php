<?php

namespace Ibrows\SimpleSearchBundle\Model;

use Doctrine\DBAL\Query\QueryBuilder;

use Symfony\Component\Form\FormInterface;

abstract class BaseSearchBuilder
{

    public function hasEmtpyData(FormInterface $form)
    {
        $value = $form->getData();
        if ($value === null) {
            return true;
        }
        if ($value === false) {
            return true;
        }
        
        return false;
    }
    
    public function getSearchClass(FormInterface $form)
    {
        return $form->getConfig()->getOption('search_class');
    }
    
    public function getSearchField(FormInterface $form)
    {
        return $form->getConfig()->getOption('search_field');
    }
    
    public function getSearchCondition(FormInterface $form)
    {
        return $form->getConfig()->getOption('search_condition');
    }
    
    public function getSearchValue(FormInterface $form)
    {
        return $form->getData();
    }
}
