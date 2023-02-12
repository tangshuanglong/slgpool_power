<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class PowerOrderPay
 *
 * @since 2.0
 *
 * @Entity(table="power_order_pay")
 */
class PowerOrderPay extends Model
{
    /**
     * 自增id
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 订单id
     *
     * @Column(name="order_id", prop="orderId")
     *
     * @var int
     */
    private $orderId;

    /**
     * 支付方式id
     *
     * @Column(name="pay_method_id", prop="payMethodId")
     *
     * @var int
     */
    private $payMethodId;

    /**
     * 实际支付金额
     *
     * @Column(name="pay_amount", prop="payAmount")
     *
     * @var string
     */
    private $payAmount;

    /**
     * 类型，1-购买，2-电费续费
     *
     * @Column()
     *
     * @var int
     */
    private $type;

    /**
     * 支付时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string
     */
    private $createdAt;


    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param int $orderId
     *
     * @return self
     */
    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @param int $payMethodId
     *
     * @return self
     */
    public function setPayMethodId(int $payMethodId): self
    {
        $this->payMethodId = $payMethodId;

        return $this;
    }

    /**
     * @param string $payAmount
     *
     * @return self
     */
    public function setPayAmount(string $payAmount): self
    {
        $this->payAmount = $payAmount;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return self
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return self
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getPayMethodId(): ?int
    {
        return $this->payMethodId;
    }

    /**
     * @return string
     */
    public function getPayAmount(): ?string
    {
        return $this->payAmount;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

}
