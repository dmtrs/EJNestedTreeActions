<?php
/**
 *  Deletenode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to delete a node in tree.
 *  Conventions:
 * The jstree must use GET to send ID of the node to delete with name 'id'.
 * If the node to delete is root it is not deleted. Will work on it in next versions.
 * See first lines of run().
 *  Callback for jstree:
 *
 * <pre>
 *       'ondelete'=>'js:function(NODE, TREE_OBJ,RB) {
 *           var dl=false;
 *           var prompt=window.confirm("Delete "+TREE_OBJ.get_text(NODE)+" ?");
 *           if( prompt ) {
 *               $.ajax({
 *                   url: "'.$this->createUrl('deletenode').'" ,
 *                   global: false,
 *                   type: "GET",
 *                   async: false,
 *                  data: ({id : NODE.id }),
 *                  success: function( dlsuccess ){
 *                       dl=dlsuccess;
 *                   }
 *               });
 *           }
 *           if ( dl ) {
 *               alert(TREE_OBJ.get_text(NODE)+" deleted.");
 *           } else {
 *               alert(TREE_OBJ.get_text(NODE)+" can not be deleted.");
 *               jQuery.tree.rollback(RB);
 *           }
 *
 *       }',
 * </pre>
 * Here i use the ondelete which means that when you will call to delete a node from
 * the tree i will dissappear from the tree but if it can not be deleted ( for example is root ),
 * it will appear again.
 * You can use the beforedelete if you don't like it what you see by changing the
 * last if in the callback with this:
 * <pre>
 *               if ( dl ) {
 *                   alert(TREE_OBJ.get_text(NODE)+" deleted.");
 *                   return true;
 *               } else {
 *                   alert(TREE_OBJ.get_text(NODE)+" can not be deleted.");
 *                   return false;
 *               }
 * </pre>
 * 
 * @version 0.1
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Deletenode extends CAction {
    public function run() {
        $id=$_GET['id'];
        $nodetodelete= CActiveRecord::model($this->getController()->classname)->findByPk($id);
        
        if ($nodetodelete->isRoot()) {
            echo false;
            die;
        }
        
        if ( $nodetodelete->deleteNode() ) {
            echo true;
        } else {
            echo false;
        }
    }
}
?>
