<?php
/**
 *  Movenode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to move a node in tree.
 *
 * Conventions:
 * The jstree must use GET to send REF_ID's id with name 'ref_id'
 * and TYPE with name 'type'
 * See first lines of run().
 * The way this is implemented is with only one root so if you try to move
 * node before or after root, it will not happen.
 * See first if.
 * Finally this action returns to the jstree the name of the node. This is
 * done because if you move a node in a place where there is a brother with
 * the same name the name of the moved node is changing ( by calling internal
 * function nodenaming, see EBehavior )
 *
 * @version 0.3beta
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Movenode extends CAction {
    public function run(){
        $id=$_POST['id'];
        $refid=$_POST['ref_id'];
        $type=$_POST['type'];
        $refnode = CActiveRecord::model($this->getController()->classname)->findByPk($refid);

        if ( $refnode->isRoot() && $type!="inside" ) {
            if ( !CActiveRecord::model($this->getController()->classname)->hasManyRoots ) {
                echo false;
                die;
            }
        }
        $current= CActiveRecord::model($this->getController()->classname)->findByPk($id);

        $parent = ($type=="inside") ? $refnode : $refnode->parent();

        $current=$this->getController()->nodeNaming($parent,$current);

        $current->saveNode();


        /**
         * $differentparent is true when the old parent is different from new else false
         * This check will be used in the if following to check if the node is changing parent
         * or is moving in under the same parent before/after brothers.
         * In second case to copy the inherit values is useless.
         */

		// trying to move before / after root (currently is not possible)
		if(!$parent) return;

        $differentparent = $parent->getAttribute($this->getController()->identity)!=$current->parent()->getAttribute($this->getController()->identity);

        if( $this->getController()->mvinherit && $differentparent ) {

            $this->getController()->inheritvalues($current,$parent);
        }

        /**
         * In all cases the $current node must be diff from the $refnode
         */
        if ( $refnode->getAttribute($this->getController()->identity)!=$current->getAttribute($this->getController()->identity) ) {
            switch ( $type ) {
                case "before":
                    $current->moveBefore($refnode);
                    break;
                case "after":
                    $current->moveAfter($refnode);
                    break;
                case "inside":
                    $current->moveAsLast($refnode);
                    break;
            }
        }


        echo $current->getAttribute($this->getController()->text);
    }
}
