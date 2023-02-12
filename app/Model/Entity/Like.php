<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 点赞表
 * Class Like
 *
 * @since 2.0
 *
 * @Entity(table="like")
 */
class Like extends Model
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
     * 接收用户id
     *
     * @Column(name="receive_user_id", prop="receiveUserId")
     *
     * @var int|null
     */
    private $receiveUserId;

    /**
     * 目标的id
     *
     * @Column(name="target_id", prop="targetId")
     *
     * @var int|null
     */
    private $targetId;

    /**
     * 目标类型：news-资讯，comment-评论
     *
     * @Column(name="target_type", prop="targetType")
     *
     * @var string|null
     */
    private $targetType;

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
