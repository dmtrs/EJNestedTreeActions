<?php
/**
 *  Createnode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to create a new node in tree.
 *  Conventions:
 * The jstree must use GET to send REF_ID's id with name 'ref_id'
 * and TYPE with name 'type'
 * See first lines of run(). 
 * 
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Createnode extends CAction {
    /**
     * This method calls the insertingnode() method from EBehavior.php
     */
    public function run(){
        $defaultname = $this->getController()->defaultNodeName;

        $refid=$_POST['ref_id'];
        $type=$_POST['type'];

        $refnode= CActiveRecord::model($this->getController()->classname)->findByPk($refid);

        $modelclass=$this->getController()->classname;
        $newnode=new $modelclass();
        $newnode->setAttribute($this->getController()->text,$defaultname);        

        echo $this->getController()->insertingnode( $newnode,$refnode,$type,true,$this->getController()->crtinherit);
    }

}