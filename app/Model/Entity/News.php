<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 资讯表
 * Class News
 *
 * @since 2.0
 *
 * @Entity(table="news")
 */
class News extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 标题
     *
     * @Column()
     *
     * @var string
     */
    private $title;

    /**
     * 缩略图
     *
     * @Column()
     *
     * @var string|null
     */
    private $thumbnail;

    /**
     * 简介
     *
     * @Column()
     *
     * @var string|null
     */
    private $summary;

    /**
     * 内容
     *
     * @Column()
     *
     * @var string|null
     */
    private $content;

    /**
     * 类型：article-文章，news_flash-快讯
     *
     * @Column(name="news_type", prop="newsType")
     *
     * @var string|null
     */
    private $newsType;

    /**
     * 资讯类型
     *
     * @Column()
     *
     * @var string
     */
    private $type;

    /**
     * 创建人ID
     *
     * @Column(name="assign_to", prop="assignTo")
     *
     * @var int
     */
    private $assignTo;

    /**
     * 状态:0-下架 1-上架
     *
     * @Column()
     *
     * @var int
     */
    private $status;

    /**
     * 语言类型:cn=中文,en=英文,kr=韩文,jp=日文
     *
     * @Column(name="lang_type", prop="langType")
     *
     * @var string
     */
    private $langType;

    /**
     * 是否精选，1-是，2-否
     *
     * @Column(name="is_featured", prop="isFeatured")
     *
     * @var int|null
     */
    private $isFeatured;

    /**
     * 排序
     *
     * @Column(name="order_num", prop="orderNum")
     *
     * @var int
     */
    private $orderNum;

    /**
     * 浏览量
     *
     * @Column(name="view_count", prop="viewCount")
     *
     * @var int|null
     */
    private $viewCount;

    /**
     * 评论量
     *
     * @Column(name="comment_count", prop="commentCount")
     *
     * @var int|null
     */
    private $commentCount;

    /**
     * 点赞量
     *
     * @Column(name="like_count", prop="likeCount")
     *
     * @var int|null
     */
    private $likeCount;

    /**
     * 利好量
     *
     * @Column(name="good_count", prop="goodCount")
     *
     * @var int|null
     */
    private $goodCount;

    /**
     * 利空量
     *
     * @Column(name="bad_count", prop="badCount")
     *
     * @var int|null
     */
    private $badCount;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
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
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string|null $thumbnail
     *
     * @return self
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param string|null $summary
     *
     * @return self
     */
    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @param string|null $content
     *
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string|null $newsType
     *
     * @return self
     */
    public function setNewsType(?string $newsType): self
    {
        $this->newsType = $newsType;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int $assignTo
     *
     * @return self
     */
    public function setAssignTo(int $assignTo): self
    {
        $this->assignTo = $assignTo;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $langType
     *
     * @return self
     */
    public function setLangType(string $langType): self
    {
        $this->langType = $langType;

        return $this;
    }

    /**
     * @param int|null $isFeatured
     *
     * @return self
     */
    public function setIsFeatured(?int $isFeatured): self
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }

    /**
     * @param int $orderNum
     *
     * @return self
     */
    public function setOrderNum(int $orderNum): self
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * @param int|null $viewCount
     *
     * @return self
     */
    public function setViewCount(?int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * @param int|null $commentCount
     *
     * @return self
     */
    public function setCommentCount(?int $commentCount): self
    {
        $this->commentCount = $commentCount;

        return $this;
    }

    /**
     * @param int|null $likeCount
     *
     * @return self
     */
    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    /**
     * @param int|null $goodCount
     *
     * @return self
     */
    public function setGoodCount(?int $goodCount): self
    {
        $this->goodCount = $goodCount;

        return $this;
    }

    /**
     * @param int|null $badCount
     *
     * @return self
     */
    public function setBadCount(?int $badCount): self
    {
        $this->badCount = $badCount;

        return $this;
    }

    /**
     * @param string|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?string $updatedAt): self
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
    public function getTitle(): ?string
    
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    
    {
        return $this->thumbnail;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    
    {
        return $this->summary;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getNewsType(): ?string
    
    {
        return $this->newsType;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getAssignTo(): ?int
    
    {
        return $this->assignTo;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getLangType(): ?string
    
    {
        return $this->langType;
    }

    /**
     * @return int|null
     */
    public function getIsFeatured(): ?int
    
    {
        return $this->isFeatured;
    }

    /**
     * @return int
     */
    public function getOrderNum(): ?int
    
    {
        return $this->orderNum;
    }

    /**
     * @return int|null
     */
    public function getViewCount(): ?int
    
    {
        return $this->viewCount;
    }

    /**
     * @return int|null
     */
    public function getCommentCount(): ?int
    
    {
        return $this->commentCount;
    }

    /**
     * @return int|null
     */
    public function getLikeCount(): ?int
    
    {
        return $this->likeCount;
    }

    /**
     * @return int|null
     */
    public function getGoodCount(): ?int
    
    {
        return $this->goodCount;
    }

    /**
     * @return int|null
     */
    public function getBadCount(): ?int
    
    {
        return $this->badCount;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    
    {
        return $this->updatedAt;
    }


}
