<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 通知表
 * Class Notification
 *
 * @since 2.0
 *
 * @Entity(table="notification")
 */
class Notification extends Model
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
     * 消息类型：announce=公告，remind=提醒，私信=message
     *
     * @Column()
     *
     * @var string|null
     */
    private $type;

    /**
     * 目标的id
     *
     * @Column(name="target_id", prop="targetId")
     *
     * @var int|null
     */
    private $targetId;

    /**
     * 目标的类型：news-资讯，comment-评论
     *
     * @Column(name="target_type", prop="targetType")
     *
     * @var string|null
     */
    private $targetType;

    /**
     * 动作类型：comment=评论，like=点赞
     *
     * @Column()
     *
     * @var string|null
     */
    private $action;

    /**
     * 发送者id
     *
     * @Column(name="sender_id", prop="senderId")
     *
     * @var int|null
     */
    private $senderId;

    /**
     * 发送者类型：user=前台用户，admin=管理员
     *
     * @Column(name="sender_type", prop="senderType")
     *
     * @var string|null
     */
    private $senderType;

    /**
     * 阅读状态：0=未读，1=已读
     *
     * @Column(name="is_read", prop="isRead")
     *
     * @var int|null
     */
    private $isRead;

    /**
     * 消息内容
     *
     * @Column()
     *
     * @var string|null
     */
    private $content;

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
     * @param string|null $type
     *
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int|null $targetId
     *
     * @return self
     */
    public function setTargetId(?int $targetId): self
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * @param string|null $targetType
     *
     * @return self
     */
    public function setTargetType(?string $targetType): self
    {
        $this->targetType = $targetType;

        return $this;
    }

    /**
     * @param string|null $action
     *
     * @return self
     */
    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param int|null $senderId
     *
     * @return self
     */
    public function setSenderId(?int $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * @param string|null $senderType
     *
     * @return self
     */
    public function setSenderType(?string $senderType): self
    {
        $this->senderType = $senderType;

        return $this;
    }

    /**
     * @param int|null $isRead
     *
     * @return self
     */
    public function setIsRead(?int $isRead): self
    {
        $this->isRead = $isRead;

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
     * @return string|null
     */
    public function getType(): ?string
    
    {
        return $this->type;
    }

    /**
     * @return int|null
     */
    public function getTargetId(): ?int
    
    {
        return $this->targetId;
    }

    /**
     * @return string|null
     */
    public function getTargetType(): ?string
    
    {
        return $this->targetType;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    
    {
        return $this->action;
    }

    /**
     * @return int|null
     */
    public function getSenderId(): ?int
    
    {
        return $this->senderId;
    }

    /**
     * @return string|null
     */
    public function getSenderType(): ?string
    
    {
        return $this->senderType;
    }

    /**
     * @return int|null
     */
    public function getIsRead(): ?int
    
    {
        return $this->isRead;
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
