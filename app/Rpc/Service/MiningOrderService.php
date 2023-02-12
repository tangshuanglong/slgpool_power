<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Rpc\Service;

use App\Model\Logic\PowerOrderLogic;
use App\Rpc\Lib\MiningOrderInterface;
use App\Rpc\Lib\UserInterface;
use Exception;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Co;
use Swoft\Rpc\Server\Annotation\Mapping\Service;

/**
 * Class UserService
 *
 * @since 2.0
 *
 * @Service()
 */
class MiningOrderService implements MiningOrderInterface
{

    /**
     * @Inject()
     * @var PowerOrderLogic
     */
    private $powerOrderLogic;

    /**
     * 订单
     * @param int $order_id
     * @param int $order_status
     * @return bool|mixed
     */
    public function order_not_pay(int $order_id)
    {
        return $this->powerOrderLogic->order_cancel($order_id);
    }
}
