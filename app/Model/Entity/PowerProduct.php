<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 产品表
 * Class PowerProduct
 *
 * @since 2.0
 *
 * @Entity(table="power_product")
 */
class PowerProduct extends Model
{
    /**
     * 自增ID
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 所属矿机ID
     *
     * @Column(name="mining_machine_id", prop="miningMachineId")
     *
     * @var int
     */
    private $miningMachineId;

    /**
     * 币种类型
     *
     * @Column(name="coin_type", prop="coinType")
     *
     * @var string
     */
    private $coinType;

    /**
     * 产品类型，1-矿机，2-算力，3-存币送算力
     *
     * @Column(name="product_type", prop="productType")
     *
     * @var int
     */
    private $productType;

    /**
     * 产品名称
     *
     * @Column(name="product_name", prop="productName")
     *
     * @var string
     */
    private $productName;

    /**
     * 总数量
     *
     * @Column(name="total_quantity", prop="totalQuantity")
     *
     * @var int
     */
    private $totalQuantity;

    /**
     * 剩余数量
     *
     * @Column(name="last_quantity", prop="lastQuantity")
     *
     * @var int
     */
    private $lastQuantity;

    /**
     * 算力
     *
     * @Column(name="product_hash", prop="productHash")
     *
     * @var string
     */
    private $productHash;

    /**
     * 实际能够封满的有效算力，矿机需要
     *
     * @Column(name="real_hash", prop="realHash")
     *
     * @var string
     */
    private $realHash;

    /**
     * 产品价格，单位 U/份或/台
     *
     * @Column()
     *
     * @var string
     */
    private $price;

    /**
     * 产品期限，单位 天
     *
     * @Column()
     *
     * @var int
     */
    private $period;

    /**
     * 管理费用，单位%
     *
     * @Column(name="manage_fee", prop="manageFee")
     *
     * @var string
     */
    private $manageFee;

    /**
     * 矿机、算力上架时间，文字描述
     *
     * @Column(name="added_time", prop="addedTime")
     *
     * @var string
     */
    private $addedTime;

    /**
     * 矿机产权， 1-赠送， 2-购买
     *
     * @Column()
     *
     * @var int
     */
    private $property;

    /**
     * 产品详情
     *
     * @Column()
     *
     * @var string|null
     */
    private $detail;

    /**
     * 产品特性
     *
     * @Column()
     *
     * @var string|null
     */
    private $feature;

    /**
     * 每人限购多少，在限购的条件下生效
     *
     * @Column()
     *
     * @var int
     */
    private $limited;

    /**
     * 是否热卖， 0-否，1-是
     *
     * @Column(name="is_sell", prop="isSell")
     *
     * @var int
     */
    private $isSell;

    /**
     * 是否限时， 0-否，1-是
     *
     * @Column(name="is_limit_time", prop="isLimitTime")
     *
     * @var int
     */
    private $isLimitTime;

    /**
     * 是否限购，0-否，1-是
     *
     * @Column(name="is_limited", prop="isLimited")
     *
     * @var int
     */
    private $isLimited;

    /**
     * 是否活动，0-否，1-是
     *
     * @Column(name="is_activity", prop="isActivity")
     *
     * @var int
     */
    private $isActivity;

    /**
     * 是否转卖，0-否，1-是
     *
     * @Column(name="is_resell", prop="isResell")
     *
     * @var int|null
     */
    private $isResell;

    /**
     * 是否推荐，0-否，1-是
     *
     * @Column(name="is_recommend", prop="isRecommend")
     *
     * @var int
     */
    private $isRecommend;

    /**
     * 是否体验，0-否，1-是
     *
     * @Column(name="is_experience", prop="isExperience")
     *
     * @var int|null
     */
    private $isExperience;

    /**
     * 状态，0-下架，1-上架
     *
     * @Column(name="status_flag", prop="statusFlag")
     *
     * @var int
     */
    private $statusFlag;

    /**
     * 区块链质押金额(FIL)
     *
     * @Column()
     *
     * @var int
     */
    private $pledge;

    /**
     * 是否需要下单的时候抵押，0-否，1-是
     *
     * @Column(name="is_pledge", prop="isPledge")
     *
     * @var int
     */
    private $isPledge;

    /**
     * 封装速度文字描述
     *
     * @Column(name="product_init_work_day", prop="productInitWorkDay")
     *
     * @var string
     */
    private $productInitWorkDay;

    /**
     * 矿工节点
     *
     * @Column(name="work_number", prop="workNumber")
     *
     * @var string|null
     */
    private $workNumber;

    /**
     * 产品标签id
     *
     * @Column(name="product_tag_ids", prop="productTagIds")
     *
     * @var string|null
     */
    private $productTagIds;

    /**
     * 开始时间
     *
     * @Column(name="start_time", prop="startTime")
     *
     * @var string|null
     */
    private $startTime;

    /**
     * 结果时间
     *
     * @Column(name="end_time", prop="endTime")
     *
     * @var string|null
     */
    private $endTime;

    /**
     * 排序
     *
     * @Column(name="order_num", prop="orderNum")
     *
     * @var int|null
     */
    private $orderNum;

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
     * @param int $miningMachineId
     *
     * @return self
     */
    public function setMiningMachineId(int $miningMachineId): self
    {
        $this->miningMachineId = $miningMachineId;

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
     * @param int $productType
     *
     * @return self
     */
    public function setProductType(int $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @param string $productName
     *
     * @return self
     */
    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @param int $totalQuantity
     *
     * @return self
     */
    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    /**
     * @param int $lastQuantity
     *
     * @return self
     */
    public function setLastQuantity(int $lastQuantity): self
    {
        $this->lastQuantity = $lastQuantity;

        return $this;
    }

    /**
     * @param string $productHash
     *
     * @return self
     */
    public function setProductHash(string $productHash): self
    {
        $this->productHash = $productHash;

        return $this;
    }

    /**
     * @param string $realHash
     *
     * @return self
     */
    public function setRealHash(string $realHash): self
    {
        $this->realHash = $realHash;

        return $this;
    }

    /**
     * @param string $price
     *
     * @return self
     */
    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param int $period
     *
     * @return self
     */
    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @param string $manageFee
     *
     * @return self
     */
    public function setManageFee(string $manageFee): self
    {
        $this->manageFee = $manageFee;

        return $this;
    }

    /**
     * @param string $addedTime
     *
     * @return self
     */
    public function setAddedTime(string $addedTime): self
    {
        $this->addedTime = $addedTime;

        return $this;
    }

    /**
     * @param int $property
     *
     * @return self
     */
    public function setProperty(int $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @param string|null $detail
     *
     * @return self
     */
    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * @param string|null $feature
     *
     * @return self
     */
    public function setFeature(?string $feature): self
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * @param int $limited
     *
     * @return self
     */
    public function setLimited(int $limited): self
    {
        $this->limited = $limited;

        return $this;
    }

    /**
     * @param int $isSell
     *
     * @return self
     */
    public function setIsSell(int $isSell): self
    {
        $this->isSell = $isSell;

        return $this;
    }

    /**
     * @param int $isLimitTime
     *
     * @return self
     */
    public function setIsLimitTime(int $isLimitTime): self
    {
        $this->isLimitTime = $isLimitTime;

        return $this;
    }

    /**
     * @param int $isLimited
     *
     * @return self
     */
    public function setIsLimited(int $isLimited): self
    {
        $this->isLimited = $isLimited;

        return $this;
    }

    /**
     * @param int $isActivity
     *
     * @return self
     */
    public function setIsActivity(int $isActivity): self
    {
        $this->isActivity = $isActivity;

        return $this;
    }

    /**
     * @param int|null $isResell
     *
     * @return self
     */
    public function setIsResell(?int $isResell): self
    {
        $this->isResell = $isResell;

        return $this;
    }

    /**
     * @param int $isRecommend
     *
     * @return self
     */
    public function setIsRecommend(int $isRecommend): self
    {
        $this->isRecommend = $isRecommend;

        return $this;
    }

    /**
     * @param int|null $isExperience
     *
     * @return self
     */
    public function setIsExperience(?int $isExperience): self
    {
        $this->isExperience = $isExperience;

        return $this;
    }

    /**
     * @param int $statusFlag
     *
     * @return self
     */
    public function setStatusFlag(int $statusFlag): self
    {
        $this->statusFlag = $statusFlag;

        return $this;
    }

    /**
     * @param int $pledge
     *
     * @return self
     */
    public function setPledge(int $pledge): self
    {
        $this->pledge = $pledge;

        return $this;
    }

    /**
     * @param int $isPledge
     *
     * @return self
     */
    public function setIsPledge(int $isPledge): self
    {
        $this->isPledge = $isPledge;

        return $this;
    }

    /**
     * @param string $productInitWorkDay
     *
     * @return self
     */
    public function setProductInitWorkDay(string $productInitWorkDay): self
    {
        $this->productInitWorkDay = $productInitWorkDay;

        return $this;
    }

    /**
     * @param string|null $workNumber
     *
     * @return self
     */
    public function setWorkNumber(?string $workNumber): self
    {
        $this->workNumber = $workNumber;

        return $this;
    }

    /**
     * @param string|null $productTagIds
     *
     * @return self
     */
    public function setProductTagIds(?string $productTagIds): self
    {
        $this->productTagIds = $productTagIds;

        return $this;
    }

    /**
     * @param string|null $startTime
     *
     * @return self
     */
    public function setStartTime(?string $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @param string|null $endTime
     *
     * @return self
     */
    public function setEndTime(?string $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @param int|null $orderNum
     *
     * @return self
     */
    public function setOrderNum(?int $orderNum): self
    {
        $this->orderNum = $orderNum;

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
     * @return int
     */
    public function getMiningMachineId(): ?int
    
    {
        return $this->miningMachineId;
    }

    /**
     * @return string
     */
    public function getCoinType(): ?string
    
    {
        return $this->coinType;
    }

    /**
     * @return int
     */
    public function getProductType(): ?int
    
    {
        return $this->productType;
    }

    /**
     * @return string
     */
    public function getProductName(): ?string
    
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getTotalQuantity(): ?int
    
    {
        return $this->totalQuantity;
    }

    /**
     * @return int
     */
    public function getLastQuantity(): ?int
    
    {
        return $this->lastQuantity;
    }

    /**
     * @return string
     */
    public function getProductHash(): ?string
    
    {
        return $this->productHash;
    }

    /**
     * @return string
     */
    public function getRealHash(): ?string
    
    {
        return $this->realHash;
    }

    /**
     * @return string
     */
    public function getPrice(): ?string
    
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getPeriod(): ?int
    
    {
        return $this->period;
    }

    /**
     * @return string
     */
    public function getManageFee(): ?string
    
    {
        return $this->manageFee;
    }

    /**
     * @return string
     */
    public function getAddedTime(): ?string
    
    {
        return $this->addedTime;
    }

    /**
     * @return int
     */
    public function getProperty(): ?int
    
    {
        return $this->property;
    }

    /**
     * @return string|null
     */
    public function getDetail(): ?string
    
    {
        return $this->detail;
    }

    /**
     * @return string|null
     */
    public function getFeature(): ?string
    
    {
        return $this->feature;
    }

    /**
     * @return int
     */
    public function getLimited(): ?int
    
    {
        return $this->limited;
    }

    /**
     * @return int
     */
    public function getIsSell(): ?int
    
    {
        return $this->isSell;
    }

    /**
     * @return int
     */
    public function getIsLimitTime(): ?int
    
    {
        return $this->isLimitTime;
    }

    /**
     * @return int
     */
    public function getIsLimited(): ?int
    
    {
        return $this->isLimited;
    }

    /**
     * @return int
     */
    public function getIsActivity(): ?int
    
    {
        return $this->isActivity;
    }

    /**
     * @return int|null
     */
    public function getIsResell(): ?int
    
    {
        return $this->isResell;
    }

    /**
     * @return int
     */
    public function getIsRecommend(): ?int
    
    {
        return $this->isRecommend;
    }

    /**
     * @return int|null
     */
    public function getIsExperience(): ?int
    
    {
        return $this->isExperience;
    }

    /**
     * @return int
     */
    public function getStatusFlag(): ?int
    
    {
        return $this->statusFlag;
    }

    /**
     * @return int
     */
    public function getPledge(): ?int
    
    {
        return $this->pledge;
    }

    /**
     * @return int
     */
    public function getIsPledge(): ?int
    
    {
        return $this->isPledge;
    }

    /**
     * @return string
     */
    public function getProductInitWorkDay(): ?string
    
    {
        return $this->productInitWorkDay;
    }

    /**
     * @return string|null
     */
    public function getWorkNumber(): ?string
    
    {
        return $this->workNumber;
    }

    /**
     * @return string|null
     */
    public function getProductTagIds(): ?string
    
    {
        return $this->productTagIds;
    }

    /**
     * @return string|null
     */
    public function getStartTime(): ?string
    
    {
        return $this->startTime;
    }

    /**
     * @return string|null
     */
    public function getEndTime(): ?string
    
    {
        return $this->endTime;
    }

    /**
     * @return int|null
     */
    public function getOrderNum(): ?int
    
    {
        return $this->orderNum;
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
