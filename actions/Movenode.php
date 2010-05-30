<?php
/**
 *  Movenode:
 * This extension of CAction is part of a the EJNestedTreeActions set.
 * It is used to move a node in tree.
 *  Conventions:
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
 *  Callback for jstree:
 * <pre>

 * </pre>
 *
 * @version 0.1
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class Movenode extends CAction {
    public function run() {
        $id=$_GET['id'];
        $refid=$_GET['ref_id'];
        $type=$_GET['type'];
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
        $current->save();
        //$this->getController()->move($current,$refnode,$type);

        switch ( $type ) {
            case "before":
                $current->moveBefore($refnode);
                break;
            case "after":
                $current->moveAfter($refnode);
                break;
            case "inside":
                /**
                 * In case user tries to move the last node of a sub-tree
                 * inside the same sub-tree then the $current and the $refnode
                 * will be the same node.
                 * Then ENestedSetBehavior will raise the
                 * "The target node should not be self." exception so we use
                 * this if to avoid it.
                 */
                if ( $current != $refnode ) {
                    $current->moveAsLast($refnode);
                }
                break;
        }
        echo $current->getAttribute($this->getController()->text);
    }

}
?>
