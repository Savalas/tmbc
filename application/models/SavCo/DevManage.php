<?php
/**
 * Created by PhpStorm.
 * User: SavCo
 * Date: 2/18/2017
 * Time: 8:47 AM
 */

class SavCo_DevManage extends SavCo
{
    //I like having this in a seperate database keeps our factual data
    //separate from the application data
    //require_once('/home8/actorsno/public_html/admin/class/contact.class.php');
    //Set sessions here can add another level by check IP Addresses
    //Can check latest code can very also one telphone input
    //city
    protected $db = null;
    public $launchemail = null;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = Zend_Registry::get('db');
    }

    public static function deleteCachedPages($config){
        //Clear Cache
        $cachedPagesDir=sprintf("%s%s",$config->paths->data,"/tmp/templates_c/");
        array_map('unlink', glob(sprintf("%s*",$cachedPagesDir)));
        return true;
    }

    public static function deleteImagesCached($config){
        //Clear Cache
        $imgFolders[]='channels';
        $imgFolders[]='channelvideos';
        $imgFolders[]='merchantsproductsoptions';
        $imgFolders[]='merchantproducts';
        $imgFolders[]='users';
        $imgFolders[]='xperiences';
        foreach ($imgFolders as $anImgFolder) {
            $cachedPagesDir = sprintf("%s%s%s/", $config->paths->data, "/thumbs/images/", $anImgFolder);
            array_map('unlink', glob(sprintf("%s*", $cachedPagesDir)));
        }
        return true;
    }

    public static function deleteImagesUploaded($config){
        //Clear Uploaded Files
        $imgFolders[]='channels';
        $imgFolders[]='channelvideos';
        $imgFolders[]='merchantproductsoptions';
        $imgFolders[]='merchantsproducts';
        $imgFolders[]='users';
        $imgFolders[]='xperiences';
        foreach ($imgFolders as $anImgFolder) {
            $cachedPagesDir = sprintf("%s%s%s/", $config->paths->data, "/uploaded-files/images/", $anImgFolder);
            array_map('unlink', glob(sprintf("%s*", $cachedPagesDir)));
        }
        return true;
    }
   //Users
    public static function truncateUsers(Zend_Db_Adapter_Abstract $db,$config){
        //DB truncate
        $db->query('TRUNCATE TABLE `users`');
        SavCo_DevManage::truncate_filesDeleteUserImages($db,$config);
        SavCo_DevManage::truncateUserProfiles($db);
        SavCo_DevManage::truncateUserPresences($db);
        SavCo_DevManage::truncateUserAddresses($db);
        SavCo_DevManage::truncateChannelDeviceConnects($db);
        SavCo_DevManage::truncateUserLocations($db);
        SavCo_DevManage::truncateUsersPromoCodes($db);
        SavCo_DevManage::truncateUsersSessions($db);
        SavCo_DevManage::truncateUsersXPLikes($db);
        SavCo_DevManage::truncateUserPayments($db);
        return true;
    }

    public static function truncate_filesDeleteUserImages(Zend_Db_Adapter_Abstract $db, $config){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_images`');

        //Remove Files
        $imageDir=sprintf("%s%s",$config->paths->data,"/uploaded-files/images/users/");
        array_map('unlink', glob(sprintf("%s*",$imageDir)));

        //Clear Cache
        $imageDir=sprintf("%s%s",$config->paths->data,"/thumbs/images/users/");
        array_map('unlink', glob(sprintf("%s*",$imageDir)));
    }
    public static function truncateUserProfiles(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_profile`');
    }
    public static function truncateUserPresences(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_presences`');
    }
    public static function truncateUserAddresses(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_addresses`');
    }

    public static function truncateUserPayments(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_payments`');
    }

    public static function truncateChannelDeviceConnects(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_channeldeviceconnects`');

    }


    public static function truncateUserLocations(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_locations`');
    }

    public static function truncateUsersPromoCodes(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_promocodes`');
    }

    public static function truncateUsersSessions(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_sessions`');
    }

    public static function truncateUsersXPLikes(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        $db->query('TRUNCATE TABLE `users_xplikes`');
    }

    //**************Merchant and MerchantProducts
    public static function truncate_filesDeleteMerchantsAndMerchantProductsAndMerchantProductOptionsAndXperiencesAndXperienceOrders(Zend_Db_Adapter_Abstract $db,$config){
        //DB truncate
        SavCo_DevManage::truncate_filesDeleteMerchantProductsAndMerchantProductOptionsAndXperiencesAndXperienceOrders($db,$config);
        $db->query('TRUNCATE TABLE `merchants`');
        //Remove Files
        $type="merchants";
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/uploaded-files/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));

        //Clear Cache
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/thumbs/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));

        $db->query('TRUNCATE TABLE `merchants_branches`');


        //and user
        SavCo_DevManage::truncateUsersXPLikes($db);
        return true;
    }

    public static function truncate_filesDeleteMerchantProductsAndMerchantProductOptionsAndXperiencesAndXperienceOrders(Zend_Db_Adapter_Abstract $db,$config){
        //DB truncate
        SavCo_DevManage::truncate_filesDeleteMerchantProductOptionsAndXperiencesAndXperienceOrders($db,$config);
        $db->query('TRUNCATE TABLE `merchant_products`');
        $db->query('TRUNCATE TABLE `merchant_products_images`');

        //Remove Files
        $type="merchantproducts";
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/uploaded-files/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));

        //Clear Cache
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/thumbs/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));
    }

    public static function truncate_filesDeleteMerchantProductOptionsAndXperiencesAndXperienceOrders(Zend_Db_Adapter_Abstract $db,$config){
        //DB truncate
        SavCo_DevManage::truncateXperiencesAndXperienceOrders($db);
        $db->query('TRUNCATE TABLE `merchant_product_options`');
        $db->query('TRUNCATE TABLE `merchant_product_options_images`');

        //Remove Files
        $type="merchantproductoptions";
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/uploaded-files/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));

        //Clear Cache
        $imageDir=sprintf("%s%s%s/",$config->paths->data,"/thumbs/images/",$type);
        array_map('unlink', glob(sprintf("%s*",$imageDir)));
    }


    public static function truncateXperiencesAndXperienceOrders(Zend_Db_Adapter_Abstract $db){
        //DB truncate
        SavCo_DevManage::truncateXperienceOrders($db);
        $db->query('TRUNCATE TABLE `xperiences`');
    }

    public static function truncateXperienceOrders(Zend_Db_Adapter_Abstract $db){
       //DB truncate
        $db->query('TRUNCATE TABLE `xperiences_orders`');
    }
}
