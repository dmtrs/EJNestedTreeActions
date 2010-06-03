<?php
/**
 *  Createnode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to create a new root in tree only if hasmanyroots for model is true. 
 *
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Createroot extends CAction { 
    public function run(){
        $hasmanyroots = CActiveRecord::model($this->getController()->classname)->hasManyRoots;		
        if($hasmanyroots){
            $defaultname = $this->getController()->defaultRootName;
            $modelclass=$this->getController()->classname;
            $newroot = new $modelclass();
            $newroot->setAttribute($this->getController()->text,$defaultname);
            $newroot = $this->getController()->nodenaming( null , $newroot );
			$newroot->setAttribute($newroot->root, 1);
            if ( $newroot->saveNode(false,null) ) {
                echo 1;die;
            }			
        }
        echo 0;die;
    }
}