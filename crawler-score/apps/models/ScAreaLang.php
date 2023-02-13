<?php

namespace Score\Models;

class ScAreaLang extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $area_id;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $area_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $area_name;

    /**
     * Method to set the value of field area_id
     *
     * @param integer $area_id
     * @return $this
     */
    public function setAreaId($area_id)
    {
        $this->area_id = $area_id;

        return $this;
    }

    /**
     * Method to set the value of field area_lang_code
     *
     * @param string $area_lang_code
     * @return $this
     */
    public function setAreaLangCode($area_lang_code)
    {
        $this->area_lang_code = $area_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field area_name
     *
     * @param string $area_name
     * @return $this
     */
    public function setAreaName($area_name)
    {
        $this->area_name = $area_name;

        return $this;
    }

    /**
     * Returns the value of field area_id
     *
     * @return integer
     */
    public function getAreaId()
    {
        return $this->area_id;
    }

    /**
     * Returns the value of field area_lang_code
     *
     * @return string
     */
    public function getAreaLangCode()
    {
        return $this->area_lang_code;
    }

    /**
     * Returns the value of field area_name
     *
     * @return string
     */
    public function getAreaName()
    {
        return $this->area_name;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("db_name_travelnercom");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sc_area_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScAreaLang[]|ScAreaLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScAreaLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
