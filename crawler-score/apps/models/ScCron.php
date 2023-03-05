<?php

namespace Score\Models;

class ScCron extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $cron_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $cron_time;
    /**
     *
     * @var integer
     */
    protected $cron_count;
    /**
     *
     * @var string
     */
    protected $cron_status;

    /**
     * Method to set the value of field area_id
     *
     * @param integer $cron_id
     * @return $this
     */
    public function setCronId($cron_id)
    {
        $this->cron_id = $cron_id;

        return $this;
    }

    /**
     * Method to set the value of field cron_time
     *
     * @param string $cron_time
     * @return $this
     */
    public function setCronTime($cron_time)
    {
        $this->cron_time = $cron_time;

        return $this;
    }

    /**
     * Method to set the value of field cron_status
     *
     * @param string $cron_status
     * @return $this
     */
    public function setCronStatus($cron_status)
    {
        $this->cron_status = $cron_status;

        return $this;
    }
    /**
     * Method to set the value of field cron_count
     *
     * @param integer $cron_count
     * @return $this
     */
    public function setCronCount($cron_count)
    {
        $this->cron_count = $cron_count;

        return $this;
    }
    /**
     * Returns the value of field area_id
     *
     * @return integer
     */
    public function getCronId()
    {
        return $this->cron_id;
    }

    /**
     * Returns the value of field cron_time
     *
     * @return string
     */
    public function getCronTime()
    {
        return $this->cron_time;
    }


    /**
     * Returns the value of field cron_status
     *
     * @return string
     */
    public function getCronStatus()
    {
        return $this->cron_status;
    }

    /**
     * Returns the value of field cron_count
     *
     * @return integer
     */
    public function getCronCount()
    {
        return $this->cron_count;
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
        return 'sc_cron';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCron[]|ScCron
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCron
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
