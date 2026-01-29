<?php
namespace accounts\domains\models;
class UserStatus {
    public $status;
    public static function get( $user , $userSentRequest  )  : string {
        if (isset($user['role']) && $user['role']=='customer'){
           $status = 'active';
        }
        else if ( isset($user['role']) && $user['role']=='admin'){
            if ($userSentRequest->role === 'superadmin') {
               $status = 'active';
            } else {
                throw new \Exception("Only superadmin can create admin users.");
            }
        }
        elseif (isset($user['role']) && $user['role']=='vendor'){
           $status = 'pending';
        }
        else{
            $status = 'inactive';
        }
        return $status ;
    }
      
}

