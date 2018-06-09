<?php
namespace app\models;
use Phalcon\Mvc\Model;
class DevItemTitleModel extends Model
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
     * @Column(type="string", length=50, nullable=false)
     */
    public $en_name;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $cn_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=3, nullable=true)
     */
    public $sort_num;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    public $width;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_frozen;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $type;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $type_value;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=true)
     */
    public $length;

    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=true)
     */
    public $align;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    public $height;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    public $is_delete;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $updated_at;

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
        return 'dev_item_title';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return DevItemTitle[]|DevItemTitle
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return DevItemTitle
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
