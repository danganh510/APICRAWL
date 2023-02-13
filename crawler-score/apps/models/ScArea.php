<?php

namespace Score\Models;

class ScArea extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $area_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $area_name;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $area_lat;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $area_lng;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $area_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $area_order;

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
     * Method to set the value of field area_lat
     *
     * @param double $area_lat
     * @return $this
     */
    public function setAreaLat($area_lat)
    {
        $this->area_lat = $area_lat;

        return $this;
    }

    /**
     * Method to set the value of field area_lng
     *
     * @param double $area_lng
     * @return $this
     */
    public function setAreaLng($area_lng)
    {
        $this->area_lng = $area_lng;

        return $this;
    }

    /**
     * Method to set the value of field area_active
     *
     * @param string $area_active
     * @return $this
     */
    public function setAreaActive($area_active)
    {
        $this->area_active = $area_active;

        return $this;
    }

    /**
     * Method to set the value of field area_order
     *
     * @param integer $area_order
     * @return $this
     */
    public function setAreaOrder($area_order)
    {
        $this->area_order = $area_order;

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
     * Returns the value of field area_name
     *
     * @return string
     */
    public function getAreaName()
    {
        return $this->area_name;
    }

    /**
     * Returns the value of field area_lat
     *
     * @return double
     */
    public function getAreaLat()
    {
        return $this->area_lat;
    }

    /**
     * Returns the value of field area_lng
     *
     * @return double
     */
    public function getAreaLng()
    {
        return $this->area_lng;
    }

    /**
     * Returns the value of field area_active
     *
     * @return string
     */
    public function getAreaActive()
    {
        return $this->area_active;
    }

    /**
     * Returns the value of field area_order
     *
     * @return integer
     */
    public function getAreaOrder()
    {
        return $this->area_order;
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
        return 'sc_area';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScArea[]|ScArea
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScArea
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
