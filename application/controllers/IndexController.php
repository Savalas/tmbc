<?
class IndexController extends CustomControllerAction{

    public function indexAction()
    {
        $commentsOrdered=DatabaseObject_Comment::GetCommentsFromId($this->db);

        $comments=$commentsOrdered;
    }


    public function addcommentAction() {
        $request =  $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->redirect("/");
        }

        $data=null;
        $fp = new FormProcessor_Comment($this->config, $this->db);
        if ($fp->process($request)) {
            $status['code'] = 200;
            $status['msg'] = "";
        } else {
            $status['code'] = 300;
            $status['msg'] = "";
            $status['errors'] = $fp->getErrors();
        }
        if($data)$response['data']=$data;
        $response['status'] = $status;

        $this->sendJson($response);
    }


    public function getcommentsAction() {
        $request =  $this->getRequest();
        if(!$request->isXmlHttpRequest()){
            $this->redirect("/");
        }

        $commentsOrdered=DatabaseObject_Comment::GetCommentsFromId($this->db);
        $data=null;
        if (true) {
            $status['code'] = 200;
            $status['msg'] = "";
            $data["comments"]=json_encode($commentsOrdered);
        }
        if($data)$response['data']=$data;
        $response['status'] = $status;

        $this->sendJson($response);
    }
}