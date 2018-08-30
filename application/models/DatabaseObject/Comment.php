<?
class DatabaseObject_Comment extends DatabaseObject{
    protected static $constTable='comments';
    protected static $constId='comment_id';

    public function __construct($db)
    {
        parent::__construct($db,DatabaseObject_Comment::$constTable,DatabaseObject_Comment::$constId);
        $this->add('comment');
        $this->add('username');
        $this->add('parent_id',0);
        $this->add('tsCreated',time());
        $this->add('tsUpdated',null);
        $this->add('tsDeleted',null);
    }

    public function arrayRepresentationSimple(){
        $simple['comment_id']=(int)$this->getId();
        $simple['comment']=$this->comment;
        $simple['username']=$this->username;
        $simple['parent_id']=$this->parent_id?(int)$this->parent_id:null;
        $simple['tsCreated']=$this->tsCreated;

        return $simple;
    }


    public static function GetCommentsFromId($db,$comment_id=null)
    {
        $comments=[];
        $comments_rendered=false;

        $select = "SELECT `comment_id`,`comment`,`username`,`parent_id`,`tsCreated` FROM `comments`";
       /// $select .="WHERE  `channel_id`=$comment_id";
        $select .=" ORDER BY `tsCreated`";

        $stmt = $db->query($select);
        $rowset = $stmt->fetchAll();

        if (count($rowset) > 0) {
            foreach ($rowset as $row) {
                //create comment
                $aComment['comment_id'] = $row['comment_id'];
                $aComment['username'] = $row['username'];
                $aComment['comment'] = $row['comment'];
                $aComment['parent_id'] = $row['parent_id'];
                $aComment['datetime'] =  sprintf('%s ago',SavCo_FunctionsGen::distanceOfTimeInWords($row['tsCreated'],time(),true));// date('m/d/Y H:i:s',$row['tsCreated']);
                $comments[]=$aComment;
            }
        }


        if(count($comments)>0) {
            $commentsOrdered = new BuildParentChild($comments);
            $comments_rendered=$commentsOrdered->render();
        }

        return $comments_rendered;
    }


    protected function postLoad()
    {
        return true;
    }

    protected function postInsert(){

        return true;
    }

    protected function postUpdate()
    {

        return true;
    }


    protected function preDelete()
    {
        return true;
    }
}