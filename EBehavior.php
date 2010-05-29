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
    public $id;
    /**
     *
     * @var string the attribute of model that will displayed
     *      as node title
     */
    public $text;
    /**
     * Used internal function that takes a node and returns it as string.
     *
     * @param CActiveRecord $model
     * @return string In jstree format.
     */
    public function formatNode($model) {
        fb("formatNode");
        $jstreeformat=array(
        'attributes'=>array(
            'id'=>$model->getAttribute($this->id)
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
            if( $bro->getAttribute($this->text) == $new->getAttribute($this->text) && $bro->getAttribute($this->id) != $new->getAttribute($this->id) ) {
                return true;
            }
        }
        return false;
    }
    /**
     * This method is used internalyy by the Createnode and Copynode actions.
     * It get's a new node to insert and depend type it insert it's appropriate
     * place relative to the refnode (reference node);
     * @param CActiverecord::model $newnode new node to insert
     * @param CActiverecord::model $refnode reference node to insertion
     * @param string $type where to insert node
     * @return CJSON::encode the node inserted in json encode
     */
    public function insertingnode( $newnode=null,$refnode=null,$type=null ) {
        if ( $type=="inside") {
            $parent=$refnode;
        } else {
            $parent=$refnode->parent();
        }
        $newnode=$this->nodeNaming($parent, $newnode);
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
                //if ( $newnode->insertBefore($refnode,false) ) {
                    $success=true;
                }
                break;
        }
        if($success){
            $jsondata=$this->formatNode($newnode);
            return CJSON::encode($jsondata);
        }

        return $success;
    }
}
?>
