<?php

namespace App\Rpc\Lib;

interface KlineInterface
{

    /**
     * @param string $coin_name
     * @param string $quote_name
     * @return string
     */
    public function get_last_close_price(string $coin_name, string $quote_name);


}
