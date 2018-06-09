<?php
namespace app\models;
use Phalcon\Mvc\Model;
class DevItemContentModel extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    public $people_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $role;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $people_status;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $system_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $system_version;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $now_stage;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $now_progress;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $begin_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $end_time;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $remark;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("develop_manage");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'dev_item_content';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return DevItemContent[]|DevItemContent
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return DevItemContent
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
