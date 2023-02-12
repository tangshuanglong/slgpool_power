<?php

namespace App\Validator;

use App\Annotation\Mapping\NotNull;
use Swoft\Validator\Annotation\Mapping\Email;
use Swoft\Validator\Annotation\Mapping\Enum;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\Min;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Range;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class MiningValidator
 * @package App\Validator
 * @Validator(name="MiningValidator")
 */
class MiningValidator
{

    /**
     * @IsInt()
     * @NotEmpty()
     * @Enum(values={1, 2, 3})
     * 1-矿机， 2-云算力，3-存币
     * @var
     */
    protected $product_type;

    /**
     * @IsInt()
     * @NotEmpty()
     * @var
     */
    protected $product_id;

    /**
     * @IsInt()
     * @NotEmpty()
     * @Min(1)
     * @var
     */
    protected $quantity;

    /**
     * @IsInt()
     * @NotEmpty()
     * @var
     */
    protected $order_id;

    /**
     * @IsString()
     * @NotEmpty()
     * @Length(min=32, max=32)
     * @var
     */
    protected $trade_pwd;

    /**
     * @IsString()
     * @NotEmpty()
     * @var
     */
    protected $pay_method;

    /**
     * @IsInt()
     * @NotEmpty()
     * @var
     */
    protected $pay_method_id;

    /**
     * @IsString()
     * @NotEmpty()
     * @var
     */
    protected $discount;


}
