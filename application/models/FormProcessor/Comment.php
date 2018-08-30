<?php
/**
 * Created by PhpStorm.
 * User: SavCo
 * Date: 8/29/2018
 * Time: 3:03 PM
 */
class FormProcessor_Comment extends FormProcessor
{
    protected $db = null;
    protected $config;
    protected $_user_id=null;
    protected $_comment=null;
    protected $_username=null;
    protected $_parent_id=null;
    protected $_commentObj=null;
    protected $_lat=null;
    protected $_lon=null;


    public function __construct(Zend_Config $config, Zend_Db_Adapter_Abstract $db,$user_id=null)
    {
        parent::__construct($user_id);
        $this->db = $db;
        $this->config=$config;
    }

    /*
    * USED WITH AJAX CALL
    */
    public function validateOnly($flag){
        $this->_validateOnly=(bool)$flag;
    }

    public function process(Zend_Controller_Request_Abstract $request)
    {
        if($theJSON=$request->getPost()){
            $this->_comment=$this->sanitize($theJSON['comment']);
            $this->_username=$this->sanitize($theJSON['username']);
            $this->_parent_id=(int)$this->sanitize($theJSON['parent_id']);
        }else{
            $this->addError('error','not supported.');
        }

        if (!$this->_comment)$this->addError('comment','comment is required.');
        if (!$this->_username)$this->addError('username','username is required.');

        if (!$this->hasError()) {
            //create comment
            $this->_commentObj = new DatabaseObject_Comment($this->db);
            $this->_commentObj->comment= $this->_comment;
            $this->_commentObj->username=$this->_username;
            if($this->_parent_id)$this->_commentObj->parent_id=$this->_parent_id;
            $this->_commentObj->save();
        }
        return !$this->hasError();
    }

    public function getComment(){
        return  $this->_commentObj;
    }
}
