<?
	class SavCo_ZendExtend extends SavCo{

      public static function Email_FromName_From_To_Data_Template_isHTML($fromName,$fromEmail,$toEmail,$dataArr,$tpl,$isHTML=false){
              try{
                  //Set values to be used in email
                  $templater= new Templater();
                  $templater->dataArr=$dataArr;
                  $templater->config=Zend_Registry::get('config');

                  //fetch the email body- error our if no template
                  $body= $templater->render('email/'.$tpl);

                  //extract the subject from the first line.
                  list($subject,$body)=preg_split('/\r|\n/',$body,2);

                  //now set-up and send the e-mail
                  $mail= new Zend_Mail('utf-8');

                  //set the to address and the user's full name in the 'to' line
                  $mail->addTo($toEmail);

                  //get the admin 'from details from the config
                  $mail->setFrom($fromEmail,$fromName);

                  //set the subject and body and send the mail
                  $mail->setSubject(trim($subject));

                  //$body = mb_convert_encoding($body, 'ISO-2022-JP', 'UTF-8');
                  if(!$isHTML){
                      $mail->setBodyText(trim($body));
                  }else{
                      $mail->setBodyHtml(trim($body));
                  }

                  //$theMail=$mail->send();

                  $SESmail = new Zend_Mail('utf-8');
                  $transport = new App_Mail_Transport_AmazonSES(
                      array(
                          'accessKey' => $templater->config->ses->accessKey,
                          'privateKey' => $templater->config->ses->privateKey
                      )
                  );


                  $SESmail->setBodyHtml($body);
                  $SESmail->setFrom($fromEmail,$fromName);
                  $SESmail->addTo($toEmail);
                  $SESmail->setSubject(trim($subject));

                  $SESmail->send($transport);

                  $message=sprintf('Email sent to %s',$toEmail);
                  SavCo_ZendExtend::LogEvent($message,'notice');

                  return true;
              }
              catch(Exception $e){
                  $message=sprintf('Not able to email %s:%s',$toEmail,$e->getMessage());
                  SavCo_ZendExtend::LogEvent($message,'warn');
              }
         }

        public static function Email_FromName_From_To_EmailSubject_EmailBody_isHTML($fromName,$fromEmail,$toEmail,$emailSubject,$emailBody,$isHTML=false){
            //There are consistent Items and Variable Items
            //The Variable Items are
            try{
                //now set-up and send the e-mail
                $mail= new Zend_Mail();

                //set the to address and the user's full name in the 'to' line
                $mail->addTo($toEmail);

                //get the admin 'from details from the config
                $mail->setFrom($fromEmail,$fromName);

                //set the subject and body and send the mail
                $mail->setSubject(trim($emailSubject));
                if(!$isHTML){
                    $mail->setBodyText(trim($emailBody));
                }else{
                    $mail->setBodyHtml(trim($emailBody));
                }

                $theMail=$mail->send();
                $message=sprintf('Db Email sent to %s',$toEmail);
                SavCo_ZendExtend::LogEvent($message,'notice');
                return true;
            }
            catch(Exception $e){
                $message=sprintf('Not able to email %s:%s',$toEmail,$e->getMessage());
                SavCo_ZendExtend::LogEvent($message,'warn');
            }
        }

        public static function Log($message,$logType='notice'){
            $logger= Zend_Registry::get('logEvent');
            switch ($logType){
                case 'notice':
                default:
                    $logger->notice($message,1);
                break;
            }

        }

        static public function LogEvent($message,$logType='notice') {
            $logger= Zend_Registry::get('logEvent');
            switch ($logType){
                case 'warn':
                    $logger->warn($message,1);
                    break;
                case 'info':
                    $logger->info($message,1);
                    break;
                case 'crit':
                    $logger->crit($message,1);
                    break;
                case 'notice':
                default:
                    $logger->notice($message,1);
                    break;
            }
        }

        static public function LogEverytime($message,$logType='notice') {
            $logger= Zend_Registry::get('logEverytime');
            switch ($logType){
                case 'warn':
                    $logger->warn($message,1);
                    break;
                case 'info':
                    $logger->info($message,1);
                    break;
                case 'crit':
                    $logger->crit($message,1);
                    break;
                case 'notice':
                default:
                    $logger->notice($message,1);
                    break;
            }
        }

        static public function LogApiResponse($message,$logType='notice') {
            $logger= Zend_Registry::get('logApiResp');
            switch ($logType){
                case 'warn':
                    $logger->warn($message,1);
                    break;
                case 'info':
                    $logger->info($message,1);
                    break;
                case 'crit':
                    $logger->crit($message,1);
                    break;
                case 'notice':
                default:
                    $logger->notice($message,1);
                    break;
            }
        }


        public static function FullUrl($url) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;
            return $url;
        }
    }