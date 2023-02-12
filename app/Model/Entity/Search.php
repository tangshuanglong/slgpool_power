<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 搜索表
 * Class Search
 *
 * @since 2.0
 *
 * @Entity(table="search")
 */
class Search extends Model
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
     * 关键字
     *
     * @Column()
     *
     * @var string|null
     */
    private $keywords;

    /**
     * 搜索量
     *
     * @Column(name="search_count", prop="searchCount")
     *
     * @var int|null
     */
    private $searchCount;


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
     * @param string|null $keywords
     *
     * @return self
     */
    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @param int|null $searchCount
     *
     * @return self
     */
    public function setSearchCount(?int $searchCount): self
    {
        $this->searchCount = $searchCount;

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
     * @return string|null
     */
    public function getKeywords(): ?string
    
    {
        return $this->keywords;
    }

    /**
     * @return int|null
     */
    public function getSearchCount(): ?int
    
    {
        return $this->searchCount;
    }


}
