<?php
namespace accounts\domains\models;
class UserStatus {
    public $status;
    public static function get( $user , $userSentRequest  )  : string {
        if (isset($user['role']) && $user['role']=='customer'){
           $status = 'active';
        }
        elseif (isset($user['role']) && ($user['role']=='vendor' || $user['role']=='admin') ){
            $status = 'pending';
            if (isset($userSentRequest) && $userSentRequest->role === 'superadmin') {
               $status = 'active';
            } 
        }
        else{
            $status = 'inactive';
        }
        return $status ;
    }
      
}

