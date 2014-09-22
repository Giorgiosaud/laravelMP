<?php
/**
 * Created by PhpStorm.
 * User: jorgelsaud
 * Date: 21/09/14
 * Time: 19:44
 */

namespace Zonapro\Mercadopago\Facades;
use Illuminate\Support\Facades\Facade;

class MP extends Facade{
    protected static function getFacadeAccessor() { return 'mercadopago'; }
} 