<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 支付方式表
 * Class PayMethod
 *
 * @since 2.0
 *
 * @Entity(table="pay_method")
 */
class PayMethod extends Model
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
     * 支付名称
     *
     * @Column(name="pay_name", prop="payName")
     *
     * @var string
     */
    private $payName;

    /**
     * 币种id
     *
     * @Column(name="coin_id", prop="coinId")
     *
     * @var int
     */
    private $coinId;

    /**
     * 折扣
     *
     * @Column()
     *
     * @var string
     */
    private $discount;

    /**
     * 开通状态，1-开通，2-尚未开通
     *
     * @Column(name="open_status", prop="openStatus")
     *
     * @var int
     */
    private $openStatus;

    /**
     * 类型：1-币种，2-人民币，3-线下
     *
     * @Column()
     *
     * @var int
     */
    private $type;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string
     */
    private $createdAt;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string
     */
    private $updatedAt;


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
     * @param string $payName
     *
     * @return self
     */
    public function setPayName(string $payName): self
    {
        $this->payName = $payName;

        return $this;
    }

    /**
     * @param int $coinId
     *
     * @return self
     */
    public function setCoinId(int $coinId): self
    {
        $this->coinId = $coinId;

        return $this;
    }

    /**
     * @param string $discount
     *
     * @return self
     */
    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @param int $openStatus
     *
     * @return self
     */
    public function setOpenStatus(int $openStatus): self
    {
        $this->openStatus = $openStatus;

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
     * @param string $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
     * @return string
     */
    public function getPayName(): ?string
    {
        return $this->payName;
    }

    /**
     * @return int
     */
    public function getCoinId(): ?int
    {
        return $this->coinId;
    }

    /**
     * @return string
     */
    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getOpenStatus(): ?int
    {
        return $this->openStatus;
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

    /**
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

}
