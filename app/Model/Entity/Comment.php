<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Comment
 *
 * @since 2.0
 *
 * @Entity(table="comment")
 */
class Comment extends Model
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
     * 用户id
     *
     * @Column(name="user_id", prop="userId")
     *
     * @var int|null
     */
    private $userId;

    /**
     * 接收者id
     *
     * @Column(name="receive_user_id", prop="receiveUserId")
     *
     * @var int|null
     */
    private $receiveUserId;

    /**
     * 父级评论id
     *
     * @Column(name="parent_comment_id", prop="parentCommentId")
     *
     * @var int|null
     */
    private $parentCommentId;

    /**
     * 回复id
     *
     * @Column(name="reply_id", prop="replyId")
     *
     * @var int|null
     */
    private $replyId;

    /**
     * 资讯id
     *
     * @Column(name="news_id", prop="newsId")
     *
     * @var int|null
     */
    private $newsId;

    /**
     * 内容
     *
     * @Column()
     *
     * @var string|null
     */
    private $content;

    /**
     * 点赞量
     *
     * @Column(name="like_count", prop="likeCount")
     *
     * @var int|null
     */
    private $likeCount;

    /**
     * 
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 
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
     * @param int|null $userId
     *
     * @return self
     */
    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param int|null $receiveUserId
     *
     * @return self
     */
    public function setReceiveUserId(?int $receiveUserId): self
    {
        $this->receiveUserId = $receiveUserId;

        return $this;
    }

    /**
     * @param int|null $parentCommentId
     *
     * @return self
     */
    public function setParentCommentId(?int $parentCommentId): self
    {
        $this->parentCommentId = $parentCommentId;

        return $this;
    }

    /**
     * @param int|null $replyId
     *
     * @return self
     */
    public function setReplyId(?int $replyId): self
    {
        $this->replyId = $replyId;

        return $this;
    }

    /**
     * @param int|null $newsId
     *
     * @return self
     */
    public function setNewsId(?int $newsId): self
    {
        $this->newsId = $newsId;

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
     * @return int|null
     */
    public function getUserId(): ?int
    
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function getReceiveUserId(): ?int
    
    {
        return $this->receiveUserId;
    }

    /**
     * @return int|null
     */
    public function getParentCommentId(): ?int
    
    {
        return $this->parentCommentId;
    }

    /**
     * @return int|null
     */
    public function getReplyId(): ?int
    
    {
        return $this->replyId;
    }

    /**
     * @return int|null
     */
    public function getNewsId(): ?int
    
    {
        return $this->newsId;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    
    {
        return $this->content;
    }

    /**
     * @return int|null
     */
    public function getLikeCount(): ?int
    
    {
        return $this->likeCount;
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
