<?php
namespace Score\Utils;
use Phalcon\Mvc\User\Component;

class Validator extends Component
{
    //validate Telephone
    public function validTel($tel)
    {
        $telReg = "/^\+?\d{1,3}[- ]?\d{3}[- ]?\d{5}$/";
        return preg_match($telReg, $tel);
    }
    //validate Telephone 2
    public function validTel2($tel)
    {
        $telReg = "/^\+?\d+$/";
        return preg_match($telReg, $tel);
    }
    //validate Email
    public function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL)!=false;
    }
    //validate Int. PHP: is_int("1") => false
    public function validInt($str)
    {
        $intReg = "/^\d+$/";
        return preg_match($intReg, $str);//return filter_var($str, FILTER_VALIDATE_INT)!=false;
    }
    //validate Domain
    public function validDomain($str)
    {
        $reg = "/^[^-]([A-Za-z0-9-]{1,63}[^-]\.)+[A-Za-z]{2,6}$/";
        return preg_match($reg, $str);
    }
    //validate Telephone
    public function validTelUser($tel)
    {
        $telReg = "/^\d{10,12}$/";
        return preg_match($telReg, $tel);
    }
    //validate Name
    public function validNameUser($name)
    {
        $nameReg = "/^[a-zA-Z ]*$/";
        return preg_match($nameReg, $name);
    }
    //validate Adress
    public function validAddressUser($address)
    {
        $addressReg = "/^\s*[a-z0-9\s]+$/i";
        return preg_match($addressReg, $address);
    }
    //accept file types
    public function accept($type)
    {
        $types = $this->globalVariable->acceptUploadTypes;
        if(isset($types[$type]))
        {
            return $types[$type];
        }
        return array();
    }
    public function acceptCV($type)
    {
        $types = $this->globalVariable->acceptUploadCV;
        if(isset($types[$type]))
        {
            return $types[$type];
        }
        return array();
    }
}

