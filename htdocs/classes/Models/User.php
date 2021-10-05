<?php

namespace classes\Models;

use Base;
use DB\SQL\Mapper;
use Log;

/**
 * This class is responsible of managing manuscript properties and method
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class User extends Mapper
{
    use ModelTrait;
    protected $attributes = ['id', 'username', 'password'];
    private $f3;

    /**
     * Constructor
     * https://fatfreeframework.com/3.7/databases#CRUD(ButWithaLotofStyle)
     */
    public function __construct()
    {
        $this->f3 = Base::instance();
        parent::__construct($this->f3->get('DB'), 'users');

        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
    }
}
