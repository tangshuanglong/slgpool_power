<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Coin
 *
 * @since 2.0
 *
 * @Entity(table="coin")
 */
class Coin extends Model
{
    /**
     * PK 货币表
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 币种名-英文全名
     *
     * @Column(name="coin_name_en_complete", prop="coinNameEnComplete")
     *
     * @var string
     */
    private $coinNameEnComplete;

    /**
     * 币种名-中文
     *
     * @Column(name="coin_name_cn", prop="coinNameCn")
     *
     * @var string
     */
    private $coinNameCn;

    /**
     * 币种的缩写，如eth,nas,dodg等
     *
     * @Column(name="coin_name_en", prop="coinNameEn")
     *
     * @var string
     */
    private $coinNameEn;

    /**
     * 充值状态  1可以充值  2充值暂停
     *
     * @Column(name="charge_status", prop="chargeStatus")
     *
     * @var int
     */
    private $chargeStatus;

    /**
     * 提现状态  1可以提现  2提现暂停
     *
     * @Column(name="get_cash_status", prop="getCashStatus")
     *
     * @var int
     */
    private $getCashStatus;

    /**
     * 币种创建时输入，发行日期，是常量
     *
     * @Column(name="public_date", prop="publicDate")
     *
     * @var string
     */
    private $publicDate;

    /**
     * 该币种的总发行量,币种创建时录入，是一个常量
     *
     * @Column(name="total_public_number", prop="totalPublicNumber")
     *
     * @var string
     */
    private $totalPublicNumber;

    /**
     * 币种的算法，币种创建时人工输入
     *
     * @Column(name="coin_algorithm", prop="coinAlgorithm")
     *
     * @var string
     */
    private $coinAlgorithm;

    /**
     * 官方钱包链接，即网址
     *
     * @Column(name="official_wallet_link", prop="officialWalletLink")
     *
     * @var string
     */
    private $officialWalletLink;

    /**
     * 官方网站链接，即网址
     *
     * @Column(name="official_website_link", prop="officialWebsiteLink")
     *
     * @var string
     */
    private $officialWebsiteLink;

    /**
     * 币种开发源码链接，即网址
     *
     * @Column(name="source_code_link", prop="sourceCodeLink")
     *
     * @var string
     */
    private $sourceCodeLink;

    /**
     * 挖矿链接，即网址
     *
     * @Column(name="mining_link", prop="miningLink")
     *
     * @var string
     */
    private $miningLink;

    /**
     * 交流论坛链接，即网址
     *
     * @Column(name="forum_link", prop="forumLink")
     *
     * @var string
     */
    private $forumLink;

    /**
     * 币种简介
     *
     * @Column(name="coin_introduction", prop="coinIntroduction")
     *
     * @var string
     */
    private $coinIntroduction;

    /**
     * 存量
     *
     * @Column()
     *
     * @var string
     */
    private $inventory;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string
     */
    private $createdAt;

    /**
     * 显隐。1：显示、0：隐藏 、2：显示不交易 （财务块显示隐藏
     *
     * @Column(name="show_flag", prop="showFlag")
     *
     * @var int
     */
    private $showFlag;

    /**
     * 开启挖矿，0-关闭，1-开启
     *
     * @Column(name="mining_enable", prop="miningEnable")
     *
     * @var int
     */
    private $miningEnable;

    /**
     * 开启支付，0-关闭，1-开启
     *
     * @Column(name="pay_enable", prop="payEnable")
     *
     * @var int
     */
    private $payEnable;

    /**
     * 开启交易，2-关闭，1-开启
     *
     * @Column(name="exchange_enable", prop="exchangeEnable")
     *
     * @var int
     */
    private $exchangeEnable;

    /**
     * 开启划转，2-关闭，1-开启
     *
     * @Column(name="transfer_enable", prop="transferEnable")
     *
     * @var int
     */
    private $transferEnable;


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
     * @param string $coinNameEnComplete
     *
     * @return self
     */
    public function setCoinNameEnComplete(string $coinNameEnComplete): self
    {
        $this->coinNameEnComplete = $coinNameEnComplete;

        return $this;
    }

    /**
     * @param string $coinNameCn
     *
     * @return self
     */
    public function setCoinNameCn(string $coinNameCn): self
    {
        $this->coinNameCn = $coinNameCn;

        return $this;
    }

    /**
     * @param string $coinNameEn
     *
     * @return self
     */
    public function setCoinNameEn(string $coinNameEn): self
    {
        $this->coinNameEn = $coinNameEn;

        return $this;
    }

    /**
     * @param int $chargeStatus
     *
     * @return self
     */
    public function setChargeStatus(int $chargeStatus): self
    {
        $this->chargeStatus = $chargeStatus;

        return $this;
    }

    /**
     * @param int $getCashStatus
     *
     * @return self
     */
    public function setGetCashStatus(int $getCashStatus): self
    {
        $this->getCashStatus = $getCashStatus;

        return $this;
    }

    /**
     * @param string $publicDate
     *
     * @return self
     */
    public function setPublicDate(string $publicDate): self
    {
        $this->publicDate = $publicDate;

        return $this;
    }

    /**
     * @param string $totalPublicNumber
     *
     * @return self
     */
    public function setTotalPublicNumber(string $totalPublicNumber): self
    {
        $this->totalPublicNumber = $totalPublicNumber;

        return $this;
    }

    /**
     * @param string $coinAlgorithm
     *
     * @return self
     */
    public function setCoinAlgorithm(string $coinAlgorithm): self
    {
        $this->coinAlgorithm = $coinAlgorithm;

        return $this;
    }

    /**
     * @param string $officialWalletLink
     *
     * @return self
     */
    public function setOfficialWalletLink(string $officialWalletLink): self
    {
        $this->officialWalletLink = $officialWalletLink;

        return $this;
    }

    /**
     * @param string $officialWebsiteLink
     *
     * @return self
     */
    public function setOfficialWebsiteLink(string $officialWebsiteLink): self
    {
        $this->officialWebsiteLink = $officialWebsiteLink;

        return $this;
    }

    /**
     * @param string $sourceCodeLink
     *
     * @return self
     */
    public function setSourceCodeLink(string $sourceCodeLink): self
    {
        $this->sourceCodeLink = $sourceCodeLink;

        return $this;
    }

    /**
     * @param string $miningLink
     *
     * @return self
     */
    public function setMiningLink(string $miningLink): self
    {
        $this->miningLink = $miningLink;

        return $this;
    }

    /**
     * @param string $forumLink
     *
     * @return self
     */
    public function setForumLink(string $forumLink): self
    {
        $this->forumLink = $forumLink;

        return $this;
    }

    /**
     * @param string $coinIntroduction
     *
     * @return self
     */
    public function setCoinIntroduction(string $coinIntroduction): self
    {
        $this->coinIntroduction = $coinIntroduction;

        return $this;
    }

    /**
     * @param string $inventory
     *
     * @return self
     */
    public function setInventory(string $inventory): self
    {
        $this->inventory = $inventory;

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
     * @param int $showFlag
     *
     * @return self
     */
    public function setShowFlag(int $showFlag): self
    {
        $this->showFlag = $showFlag;

        return $this;
    }

    /**
     * @param int $miningEnable
     *
     * @return self
     */
    public function setMiningEnable(int $miningEnable): self
    {
        $this->miningEnable = $miningEnable;

        return $this;
    }

    /**
     * @param int $payEnable
     *
     * @return self
     */
    public function setPayEnable(int $payEnable): self
    {
        $this->payEnable = $payEnable;

        return $this;
    }

    /**
     * @param int $exchangeEnable
     *
     * @return self
     */
    public function setExchangeEnable(int $exchangeEnable): self
    {
        $this->exchangeEnable = $exchangeEnable;

        return $this;
    }

    /**
     * @param int $transferEnable
     *
     * @return self
     */
    public function setTransferEnable(int $transferEnable): self
    {
        $this->transferEnable = $transferEnable;

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
    public function getCoinNameEnComplete(): ?string
    {
        return $this->coinNameEnComplete;
    }

    /**
     * @return string
     */
    public function getCoinNameCn(): ?string
    {
        return $this->coinNameCn;
    }

    /**
     * @return string
     */
    public function getCoinNameEn(): ?string
    {
        return $this->coinNameEn;
    }

    /**
     * @return int
     */
    public function getChargeStatus(): ?int
    {
        return $this->chargeStatus;
    }

    /**
     * @return int
     */
    public function getGetCashStatus(): ?int
    {
        return $this->getCashStatus;
    }

    /**
     * @return string
     */
    public function getPublicDate(): ?string
    {
        return $this->publicDate;
    }

    /**
     * @return string
     */
    public function getTotalPublicNumber(): ?string
    {
        return $this->totalPublicNumber;
    }

    /**
     * @return string
     */
    public function getCoinAlgorithm(): ?string
    {
        return $this->coinAlgorithm;
    }

    /**
     * @return string
     */
    public function getOfficialWalletLink(): ?string
    {
        return $this->officialWalletLink;
    }

    /**
     * @return string
     */
    public function getOfficialWebsiteLink(): ?string
    {
        return $this->officialWebsiteLink;
    }

    /**
     * @return string
     */
    public function getSourceCodeLink(): ?string
    {
        return $this->sourceCodeLink;
    }

    /**
     * @return string
     */
    public function getMiningLink(): ?string
    {
        return $this->miningLink;
    }

    /**
     * @return string
     */
    public function getForumLink(): ?string
    {
        return $this->forumLink;
    }

    /**
     * @return string
     */
    public function getCoinIntroduction(): ?string
    {
        return $this->coinIntroduction;
    }

    /**
     * @return string
     */
    public function getInventory(): ?string
    {
        return $this->inventory;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getShowFlag(): ?int
    {
        return $this->showFlag;
    }

    /**
     * @return int
     */
    public function getMiningEnable(): ?int
    {
        return $this->miningEnable;
    }

    /**
     * @return int
     */
    public function getPayEnable(): ?int
    {
        return $this->payEnable;
    }

    /**
     * @return int
     */
    public function getExchangeEnable(): ?int
    {
        return $this->exchangeEnable;
    }

    /**
     * @return int
     */
    public function getTransferEnable(): ?int
    {
        return $this->transferEnable;
    }

}
