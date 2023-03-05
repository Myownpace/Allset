<?php
require_once "basics.php";

class sanitize{
    public static function number($number,bool $clean){
        $s_num = (int) $number;
        return $clean? $s_num : ($number == $s_num);
    }
}

class extvar{
    public static function var($SG,...$post_vars) : array{
        /*returns all the post variables given to it, if any of the variables is 
        an array then the first element is taken as the variable and the second 
        is taken as a value to assign to the element if the SG variable doesnt 
        exist,
        if the array has only one element, then the variable is treated like it
        isn't an array,
        if the array isn't an indexed array then a type error will be thrown
        the SG parameter specifies what superglobal to search
        */
        $result_array = [];
        foreach($post_vars as $index=>$var){
            $index = $var; $alt_var = null;
            if(is_array($var)){
                $index = isset($var[0])? $var[0] : null;
                $alt_var = isset($var[1])? $var[1] : null;
            }
            if(!is_keyable($index)){throw new Exception("invalid array key");}
            $var = isset($SG[$index])? $SG[$index] : $alt_var;
            $result_array[$index] = $var;
        }
        return $result_array;
    }
    public static function password($password,int $min_len = 8,
    int $require_number = 1,int $require_symbol = 1){
        /*rules 
         # minimum character length of password is specified by $min_len
         # minimum numbers required in password is $require_number
         # minimum symbols required in password is $require_symbol
        */
        $length = mb_strlen($password);
        if(is_empty($password)){
            return [
                "status"=>false,
                "reason"=>"Your password cannot be empty"
            ];
        }
        if($length < $min_len){return 
            ["status"=>false,
            "reason"=>"Password is too short, your password should not be 
            less than $min_len characters long"];
        }
        if($require_number){
            $numbers = preg_match_all("/\d/",$password);
            if($numbers < $require_number){
                $proper = $require_number > 1? "$require_number numbers" : 
                "$require_number number";
                return [
                    "status"=>false,
                    "reason"=>"Password should contain at least $proper"
                ];
            }
        }
        if($require_symbol){
            $symbols = preg_replace("/\w/","",$password);
            $symbols = preg_replace("/\s/","",$symbols);
            if(mb_strlen($symbols) < $require_symbol){
                $proper = $require_symbol > 1? "$require_symbol symbols" :
                "$require_symbol symbol";
                return [
                    "status"=>false,
                    "reason"=>"Password should contain at least $proper"
                ];
            }
        }
        return true;
    }
    public static function phone($phone_number){
        $phone_number = preg_replace("/\s/","",$phone_number);
        if(is_empty($phone_number,true)){
            return ["stat"=>false,"reason"=>"Phone number cannot be empty"];
        }
        $phone_number = preg_replace("/[\d+]/","",$phone_number);
        if(!is_empty($phone_number,true)){
            return [
                "stat"=>false,
                "reason"=>"invalid phone number given"
            ];
        }
        return true;
    }
}
?>