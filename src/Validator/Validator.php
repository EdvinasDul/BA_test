<?php

namespace App\Validator;

class Validator{
    # phone number validator
    public function validate($obj){
        $errors = [];
        $minLengthPhone = 8;
        $maxLengthPhone = 20;
        $maxLengthName = 40;

        // ----- Full name validation -----
        // check length
        if($obj->getFullName() > $maxLengthName){
            $message = "Name is too long!";
            array_push($errors, $message);
        }

        // check for special characters
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_¬+]/', $obj->getFullName())){
            $message = "No special characters alowed in full name! You can use only '-' to separate middle name";
            array_push($errors, $message);
        }

        // check for numbers
        if(preg_match("/[0-9]/", $obj->getFullName())){
            $message = "Full name can contain only letters!";
            array_push($errors, $message);
        }

        // ----- Phonen number validation -----
        //eliminate every char except 0-9
        $number = preg_replace("/[^0-9]/", '', $obj->getPhoneNumber());
        
        // check length
        if(strlen($number) < $minLengthPhone){
            $message = "Phone number has to be at least 8 digits long and not longer than 20!";
            array_push($errors, $message);
        }

        // check length
        if(strlen($number) > $maxLengthPhone){
            $message = "Phone number has to be at least 8 digits long and not longer than 20!";
            array_push($errors, $message);
        }

        // check for special characters
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_¬-]/', $obj->getPhoneNumber())){
            $message = "No special characters alowed in phone number! You can use only '+' in the beginning";
            array_push($errors, $message);
        }

        // check for letters
        if(preg_match("/[a-zA-Z]/", $obj->getPhoneNumber())){
            $message = "Phone number can contain only numbers!";
            array_push($errors, $message);
        }

        return $errors;
    }
}