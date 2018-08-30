<?
    abstract class FormProcessor
    {
        protected $_errors = array();
        protected $_vals = array();
        protected $_user_id=null;
        protected $_tok=null;
        private $_sanitizeChain = null;

        public function __construct($user_id=null)
        {
            $this->_user_id=$user_id;
        }

       	abstract function process(Zend_Controller_Request_Abstract $request);
       	
        public function sanitize($value)
        {
            if (!$this->_sanitizeChain instanceof Zend_Filter) {
                $this->_sanitizeChain = new Zend_Filter();
                $this->_sanitizeChain->addFilter(new Zend_Filter_StringTrim())
                                     ->addFilter(new Zend_Filter_StripTags());
            }

            // filter out any line feeds / carriage returns
            $ret = preg_replace('/[\r\n]+/', ' ', $value);

            // filter using the above chain
            return $this->_sanitizeChain->filter($ret);
        }

        public function addError($key, $val)
        {
            if (array_key_exists($key, $this->_errors)) {
                if (!is_array($this->_errors[$key]))
                    $this->_errors[$key] = array($this->_errors[$key]);
                	$this->_errors[$key][] = $val;
            }
           	else
                $this->_errors[$key] = $val;
        }
        
         public function flushErrors($outputText)
        {
        	if ($this->hasError()){
            	if($outputText){
             		foreach($this->_errors as $key => $value){
  						echo "$key : $value";           	
             		}
             	}
             	$this->_errors=array();
            }
        }

        public function getError($key)
        {
		    if ($this->hasError($key)){
            	return $this->_errors[$key];
				return 'Error '.substr($key,0,1);	
            }
         	return $this->_errors[$key];
        }

        public function getErrors()
        {
            return $this->_errors;
        }

        public function hasError($key = null)
        {	
            if (strlen($key) == 0)
                return count($this->_errors) > 0;

            return array_key_exists($key, $this->_errors);
        }

        public function __set($name, $value)
        {
            $this->_vals[$name] = $value;
        }

        public function __get($name)
        {
            return array_key_exists($name, $this->_vals) ? $this->_vals[$name] : null;
        }


        public function userAuth($isRequired=false){
            $isAuthValid=true;
            if(!$this->_user_id){
                if (trim($this->_tok)) {
                    try {                       //call via the API
                        $tok = JWT::decode(trim($this->_tok), $this->config->jwt->secret);
                        $this->_user_id = $tok->id;
                    } catch (Exception $e) {
                        //Invalid toke
                        if($isRequired){
                            $isAuthValid=false;
                            $this->addError('user_id', 'Invalid user.');
                        }
                    }
                }else{
                    if($isRequired){
                        $isAuthValid=false;
                        $this->addError('user_id', 'User is required');
                    }
                }
            }
            return $isAuthValid;
        }
    }
?>