<?php
/**
 *  Deletenode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to delete a node in a tree.
 * 
 * Conventions:
 * The jstree must use GET to send ID of the node to delete with name 'id'.
 *  
 * See first lines of run(). 
 * 
 * @version 0.1
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Deletenode extends CAction {
    public function run(){
        $id=$_POST['id'];
        $nodetodelete= CActiveRecord::model($this->getController()->classname)->findByPk($id);               
        
        echo $nodetodelete->deleteNode();
    }
}