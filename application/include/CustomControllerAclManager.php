<?
    class CustomControllerAclManager extends Zend_Controller_Plugin_Abstract{
        // default user role if not logged
        private $_defaultRole ="";

        // the action to dispatch if a user doesn't have sufficient privileges
        //Will be different
        private $_authController = array();

        public function __construct(Zend_Auth $auth)
        {  //SETS UP PERMISSION FOR USERS AND RESOURCES
            $appConfig=Zend_Registry::get('config');

            $this->_defaultRole=$appConfig->access->defaultuser;
            $this->_authController = array('controller' => $appConfig->access->defaultcontroller,
                                            'action' => $appConfig->access->defaultaction);

            $this->auth = $auth;
            $this->acl = new Zend_Acl();

            //****** USER ROLES********			
			//Default User
            $this->acl->addRole(new Zend_Acl_Role($this->_defaultRole));

            //ROLES
            $this->acl->addRole(new Zend_Acl_Role('member'));
            $this->acl->addRole(new Zend_Acl_Role('Admin'), 'member');
            $this->acl->addRole(new Zend_Acl_Role('developer'), 'Admin');

            //******** RESOURCES ************
            /** @noinspection PhpDeprecationInspection */
            $this->acl->addResource(new Zend_Acl_Resource('index'));
            $this->acl->addResource(new Zend_Acl_Resource('webrtc'));


  			//allow access to everything for all users by default
            //this includes the policy pages for terms, privacy, aboutus
            $this->acl->allow();
    		
            //Permission for Controllers


            //Deny Apis
			//Public Profile Prevention -Star Profiles
			//$this->acl->deny('newMember', 'user');
			//$this->acl->deny('member', 'user');
			// add an exception so guests can log in or register
            // in order to gain privilege

            //PERMISSIONS- GUEST
            $this->acl->allow('guest', 'index');

        }

        /**
         * preDispatch
         *
         * Before an action is dispatched, check if the current user
         * has sufficient privileges. If not, dispatch the default
         * action instead
         *
         * @param Zend_Controller_Request_Abstract $request
         */
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            // check if a user is logged in and has a valid role,
            // otherwise, assign them the default role (guest)
            if ($this->auth->hasIdentity()){
                $identity=$this->auth->getIdentity();
                $role = $identity->user_type;

            }
            else
                $role = $this->_defaultRole;

            if (!$this->acl->hasRole($role))
                $role = $this->_defaultRole;

            // the ACL resource is the requested controller name
            $resource = $request->controller;

            // the ACL privilege is the requested action name
            $privilege = $request->action;

            // if we haven't explicitly added the resource, check
            // the default global permissions
            if (!$this->acl->has($resource))
        				$resource= null;	

            if (!$this->acl->isAllowed($role, $resource, $privilege)) {
                $request->setControllerName($this->_authController['controller']);
                $request->setActionName($this->_authController['action']);
			}						
        }
    }