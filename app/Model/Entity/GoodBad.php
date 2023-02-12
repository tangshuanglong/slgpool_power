<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 利好利空表
 * Class GoodBad
 *
 * @since 2.0
 *
 * @Entity(table="good_bad")
 */
class GoodBad extends Model
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
     * 资讯id
     *
     * @Column(name="news_id", prop="newsId")
     *
     * @var int|null
     */
    private $newsId;

    /**
     * 类型：good=利好，bad=利空
     *
     * @Column()
     *
     * @var string|null
     */
    private $type;

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
    public function getNewsId(): ?int
    
    {
        return $this->newsId;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    
    {
        return $this->type;
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
