<?php
/**
 * Description of EBehavior
 * This behavior has some functions used in the Actions and
 * initializes the following: classname, id, text.
 * For more info check in code.
 *
 * Example (add this to the controller class):
 * public function behaviors() {
 *   return array(
 *       'EJNestedTreeActions'=>array(
 *           'class'=>'ext.EJNestedTreeActions.EBehavior',
 *           'classname'=>'Rutree',
 *           'id'=>'id',
 *           'text'=>'text',
 *       ),
 *   );
 * }
 * 
 * @version 0.1
 * @author Dimitrios Meggidis <tydeas.dr@gmail.com>
 * @copyright Evresis A.E. <www.evresis.gr>
 */
class EBehavior extends CBehavior {
    /**
     * @var string the name of the model class
     */
    public $classname;
    /**
     * @var string the name of the id attribute of model
     */
    public $identity;
    /**
     *
     * @var string the attribute of model that will displayed
     *      as node title
     */
    public $text;
    /**
     * @var array the attributes that will be inherited from parent to children
     */
    public $inherit;
    /**
     * The following boolean attributes are have same behavior for different
     * senarios:
     * mvinherit is for moving nodes.
     * cpinherit is for copying nodes.
     * crtinherit is for creating node.
     *
     * If true at the senario the nodes will inherit the inherit values from
     * the new parent
     * @var boolean
     */
    public $mvinherit;
    public $cpinherit;
    public $crtinherit;
    /**
     * If when creating node should check the future brothers for same name and change the new
     * node name to old i++
     * @var boolean
     */
    public $nodenaming;
    /**
     * Used internal function that takes a node and returns it as string.
     *
     * @param CActiveRecord $model
     * @return string In jstree format.
     */

    public function formatNode($model) {
        $jstreeformat=array(
        'attributes'=>array(
            'id'=>$model->getAttribute($this->identity)
        ),
            'data'=>$model->getAttribute($this->text),
        );
        if(!$model->isLeaf()) $jstreeformat['state']="closed";
            return $jstreeformat;
    }
     /**
      * This method search for brother nodes with same name.
      * Used internally when trying to rename,create or move
      * @param CActiveRecord $parent the parent of the node
      * @param CActiveRecord $node the node that' been created/moved/renamed
      * @return CActiveRecord $node the node with the proper name
      */
    public function nodeNaming($parent,$node){

        $brothers=$parent->children()->findAll(array(
            'condition'=>'`'.$this->text.'` LIKE :dfname',
            'params'=>array(':dfname'=>$node->getAttribute($this->text).'%'),
            )
        );

        $name=$node->getAttribute($this->text);
        $i=1;
        $namenotfound=true;
        do {
            $namenotfound=$this->nameExist($brothers,$node);
            if ($namenotfound) {
                $node->setAttribute($this->text,$name." ".$i);
                $i++;
            }
        } while($namenotfound);
        return $node;
    }
    /**
     * This method is used internally. It takes the an array of the nodes and
     * second node as well.
     * At create node the array of nodes is the possible brothers of the new node.
     * Because new node has not an id yet the second part will return true.     
     * At rename the array of nodes contains the new node as well this is why the 
     * second part of if checks if the $bro id is different from the $new id.
     * The second part of if is needed for the move node action too.
     * @param CActiveRecord $bro
     * @param CActiveRecord $new
     * @return boolean true if name already exist false if not exist
     */
    public function nameExist($bro,$new){
        foreach( $bro as $i=>$bro ){
            if( $bro->getAttribute($this->text) == $new->getAttribute($this->text) && $bro->getAttribute($this->identity) != $new->getAttribute($this->identity) ) {
                return true;
            }
        }
        return false;
    }
    /**
     * This method is used internaly by the Createnode and Copynode actions.
     * It get's a new node to insert and depend type it insert it's appropriate
     * place relative to the refnode (reference node);
     * @param CActiverecord::model $newnode new node to insert
     * @param CActiverecord::model $refnode reference node to insertion
     * @param string $type where to insert node
     * @param boolean $nodenaming if there should be a nodenaming check default true
     * @param boolean $inheritvalues if inherit values must be copied or not.
     * @return CJSON::encode the node inserted in json encode
     */
    public function insertingnode( $newnode=null,$refnode=null,$type=null, $nodenaming=true , $inheritvalues=false ) {
//        if ( $type=="inside") {
//            $parent=$refnode;
//        } else {
//            $parent=$refnode->parent();
//        }
        $parent = ($type=="inside") ? $refnode : $refnode->parent();

        $newnode= ($nodenaming) ? $this->nodeNaming($parent, $newnode) : $newnode;

        $success=false;
        switch ( $type ) {
            case "inside":
                if ( $refnode->append($newnode,false) ) {
                    $success=true;
                }
                break;
            case "before":
                if ( $newnode->insertBefore($refnode,false) ) {
                    $success=true;
                }
                break;
            case "after":
                if ( $newnode->insertAfter($refnode,false) ) {
                    $success=true;
                }
                break;
        }
        if($success) {
            if($inheritvalues) {
                fb("inherit");
                $this->inheritvalues( $newnode , $parent );
            }
            $jsondata=$this->formatNode($newnode);
            return CJSON::encode($jsondata);
        }

        return $success;
    }
    /**
     * This methond is used mainly from the copynode action. It copies a node
     * and his child if there are exist.
     * @param string $id The id of the node to be copied
     * @param string $ref The id of a reference node that is used with type to determine the new position
     * @param string $type Where the new node will be copied
     */
    public function copytree( $id, $ref, $type ){
        $classname=$this->classname;

        $node=CActiveRecord::model($this->classname)->findByPk($id);
        $refnode=CActiveRecord::model($this->classname)->findByPk($ref);

        if($node->isLeaf()) {
            $copy=new $classname();
            $copy->attributes=$node->attributes;
            $this->insertingnode( $copy,$refnode,$type,true,$this->cpinherit );
        } else {
            $copy=new $classname();
            $copy->attributes=$node->attributes;

            $this->insertingnode( $copy,$refnode,$type,true,$this->cpinherit );
            $childs = $node->children()->findall();
            foreach( $childs as $i => $chnode ) {
                $this->copytree( $chnode->getAttribute($this->identity) , $copy->getAttribute($this->identity) , "inside" , false  );
            }
        }


        //echo $this->getController()->copytree($copy,$refnode,$type);
        

    }
    /**
     * This method is used to at create/copy or move. If the last $inherit attribute is true
     * the nodes under current and current also will inherit the values from the new parent.
     * @param CActiveRecord $current node that is created/copied or moved
     * @param CActiveRecord $parent the new parent in either senario
     */
    public function inheritvalues( $current , $parent ) {
    /**
     * $differentparent is true when the old parent is different from new else false
     * This check will be used in the if following to check if the node is changing parent
     * or is moving in under the same parent before/after brothers.
     * In second case to copy the inherit values is useless.
     */
//        $differentparent = $parent->getAttribute($this->identity)!=$current->parent()->getAttribute($this->identity);
//        fb($differentparent);
//        if ( $differentparent ) {
            fb("in 1st if");
            foreach ( $this->inherit as $attr ) {
                fb("currnet");
                $current->setAttribute($attr,$parent->getAttribute($attr));
            }
            $current->saveNode();
            $descendants = $current->descendants()->findAll();
            foreach ( $descendants as $i => $node ) {
                fb("descendants");
                foreach ( $this->inherit as $attr ) {
                    $node->setAttribute($attr,$parent->getAttribute($attr));
                }
                $node->saveNode();
            }
        //}
    }
}
?>
