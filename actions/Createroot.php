<?php
/**
 *  Createnode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to create a new root in tree only if hasmanyroots for model is true.
 *  In this method there is no need for a callback. You will need this ajaxbutton
 * to make the call:
 * echo CHtml::ajaxButton("Create root",$this->createurl('createroot'), array ('global'=>false,
 *                   'type' => "GET",
 *                   'async'=> false,'success'=>'js:function(bool) {
 *                       if (bool) {
 *                           $.tree.focused().refresh();
 *                       } else {
 *                           alert("You can not create root");
 *                       }
 *           }' ), array ( 'id'=>'createroot'));
 *
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Createroot extends CAction {
 
    public function run( ) {
        $hasmanyroots = CActiveRecord::model($this->getController()->classname)->hasManyRoots;
        if($hasmanyroots){
            $defaultname = ( isset($this->getController()->defaultname) ) ? $this->getController()->defaultname : "New R1oot" ;
            $modelclass=$this->getController()->classname;
            $newroot = new $modelclass();
            $newroot->setAttribute($this->getController()->text,$defaultname);
            $newroot = $this->getController()->nodenaming( null , $newroot );
            if ( $newroot->saveNode(false,null) ) {
                echo 1;die;
            }
        }
        echo 0;die;

    }

}
?>
