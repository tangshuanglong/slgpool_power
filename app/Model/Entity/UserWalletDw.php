<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class UserWalletDw
 *
 * @since 2.0
 *
 * @Entity(table="user_wallet_dw")
 */
class UserWalletDw extends Model
{
    /**
     * 充值提现余额表
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 用户ID，需要缓存
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 币种
     *
     * @Column(name="coin_id", prop="coinId")
     *
     * @var int
     */
    private $coinId;

    /**
     * 币种类型名称
     *
     * @Column(name="coin_type", prop="coinType")
     *
     * @var string
     */
    private $coinType;

    /**
     * 可用币的数量
     *
     * @Column(name="free_coin_amount", prop="freeCoinAmount")
     *
     * @var float|null
     */
    private $freeCoinAmount;

    /**
     * 冻结币的数量
     *
     * @Column(name="frozen_coin_amount", prop="frozenCoinAmount")
     *
     * @var float|null
     */
    private $frozenCoinAmount;


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
     * @param int $uid
     *
     * @return self
     */
    public function setUid(int $uid): self
    {
        $this->uid = $uid;

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
     * @param string $coinType
     *
     * @return self
     */
    public function setCoinType(string $coinType): self
    {
        $this->coinType = $coinType;

        return $this;
    }

    /**
     * @param float|null $freeCoinAmount
     *
     * @return self
     */
    public function setFreeCoinAmount(?float $freeCoinAmount): self
    {
        $this->freeCoinAmount = $freeCoinAmount;

        return $this;
    }

    /**
     * @param float|null $frozenCoinAmount
     *
     * @return self
     */
    public function setFrozenCoinAmount(?float $frozenCoinAmount): self
    {
        $this->frozenCoinAmount = $frozenCoinAmount;

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
    public function getUid(): ?int
    {
        return $this->uid;
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
    public function getCoinType(): ?string
    {
        return $this->coinType;
    }

    /**
     * @return float|null
     */
    public function getFreeCoinAmount(): ?float
    {
        return $this->freeCoinAmount;
    }

    /**
     * @return float|null
     */
    public function getFrozenCoinAmount(): ?float
    {
        return $this->frozenCoinAmount;
    }

}
