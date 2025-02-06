<?php

namespace App\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\SpecificDiscountItem;
use Illuminate\Support\Facades\Schema;
use Modules\Report\Http\Controllers\Admin\ReportController;
use Modules\Store\Entities\Store;
use Modules\Store\Http\Controllers\Admin\StoreController;

class testController extends Controller
{

    public function addImageAltToProductsTable()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_alt', 191)->nullable();
        });
    }

    public function index()
    {
        $zeroBalanceVarietyIdsInStore = Store::query()->select(['variety_id'])->where('balance', '=', 0)->pluck('variety_id')->toArray();
        Product::query()->where('status', Product::STATUS_OUT_OF_STOCK)
            ->select(['id','status'])
//            ->where('id', 26)
//            ->whereNotIn('id', [25])
            ->with(['varieties' => function($query) { return $query->select('id'); }])
            ->chunk(50, function ($products) use ($zeroBalanceVarietyIdsInStore) {
                foreach ($products as $product) {
                    $hasAvailableVariety = false;
//                    $varietyIds = $product->varieties()->pluck('id')->toArray();
//                    foreach ($varietyIds as $varietyId) {
//                        if (!in_array($varietyId, $zeroBalanceVarietyIdsInStore)) {
//                            $hasAvailableVariety = true;
//                            break;
//                        }
//                    }

                    if (!$hasAvailableVariety) {
                        $product->categories()->detach();
                    }
                }
            });




        dd('hello multiStores');
//        \request()->header('accept') = 'x-xlsx';
        \request()->headers->set('accept','x-xlsx');
        return (new StoreController())->storeBalanceReport();
        dd('FINISHED IN TESTCONTROLLER');
        $percentage50 = [4377,4330,2954,3011,3892,3144,2351,733,3792,3452,3055,2848,3360,3473,3013,3403,3162,3181,3354,2889,2890,2905,3027,3089,3023,2766,3185,3041,2882,2763,2881,2799,2970,2888,2788,3044,3022,2887,3189,2765,3053,3026,3028,3614,3604,2933,3066,3195,3233,3435,3582,3153,3780,3093,2941,3090,3137,2796,4004,2912,2930,2965,2957,3060,3177,3187,3570,3064,3613,3624,3362,3371,3358,3096,3229,3430,3431,2919,3627,2918,3622,3412,3574,2981,3960,3987,2278,3997,4047,4055,4400,3887,3158,2771,1550,3790,2952,3200,4027,3914,3593,4000,3190,3779,3883,3646,3921,4060,3923,4024,4045,4065,4141,4158,4211,4188,4195,4229,4466,4468,4510,4547,3850,3126,3829,3677,4008,4015,2414,3598,3680,3916,4155,4029,1882,2808,3678,4068,3637,4307,3391,3885,3944,3666,2507,4092,4057,3625,3660,3699,3700,3912,3920,4056,4013,4114,4121,4167,4187,4206,4204,4228,4241,4240,4279,4315,4319,4327,4325,4379,4408,4460,4489,4490,4495,4512,4523,4527,4212,3565,3572,3929,4174,2755,4116,3626,3935,3837,3193,4104,3662,3559,3932,3957,3740,3650,3938,4146,4261,4264,3756,3629,3634,3580,3578,4095,4175,4244,4394,3413,3669,3673,3339,3576,3663,2729,3014,2767,3398,1214,2756,2800,3346,3597,3481,2972,3654,4392,3180,3553,3581,3556,4317,3243,4499,4530,3071,3390,3486,3870,4341,3206,4058,3835,3791,3117,3471,4300,4296,4288,3690,3693,3254,3465,3458,3531,3045,3674,4169,4339,3897,3868,4237,4243,4224,2722,2932,3173,3263,4448,2988,3157,3152,3584,2361,3534,3577,4012,4189,2945,3340,3623,4335,3902,1676,1699,3179,4025,4198,3184,3080,3474,4411,4399,3507,2929,4262,4401,3772,3859,4404,4453,2461,3686,3777,3828,3919,2769,3711,3714,3854,3840,3972,3979,4049,4192,4321,4454,3952,3511,3689,4046,3684,1076,3709,4184,3617,3710,3893,3939,3720,3105,3169,2805,4178,4193,4200,4529,2728,3466,3606,2983,3784,3037,4073,4111,4127,3163,3165,3164,3788,3786,3787,4160,4493,3154,2804,3012,3524,4382,3007,842,3896,3383,3552,3841,3142,2500,4359,3842,2410,2951,2846,3107,3249,3075,3175,3166,3438];
        $percentage40 = [2971,3852,4061];
        $percentage30 = [4351,4538,3962,3774,4558,4542,4331,4475,3223,3814,3212,3585,3211,4079,3501,3467,3734,4362,3820,3821,3460,3478,3910,4163,3236,3244,3733,3213,3241,3239,4098,3518,3514,4112,3961,4048,4064,4375,4492,4459,3872,3819,3500,3884,3813,3809,3736,3727,4182,4091,3853,3924,4277,4102,4478,4066,4251,4196,4253,4281,4306,4365,4369,4462,4511,4540,4569,4589,3220,3235,3830,3681,3210,3679,4371,3440,3794,3981,4302,3707,3816,4134,3811,3759,3798,3795,3927,3728,3748,3871,3838,3858,3933,3867,3922,3958,3945,4125,4040,4161,4173,4285,4276,4275,4303,4209,4271,4328,4326,4353,4361,4376,4373,4477,4515,4503,4513,4522,4509,4528,4551,4564,4576,4584,3915,3806,4406,4457,4536,4556,3980,3596,3822,3548,3590,3549,3442,3726,4078,3724,3492,4393,3225,3797,4318,4519,4539,3704,4342,4144,3222,3807,3768,3907,4283,4101,4578,3529,3246,3803,4443,3245,3521,4560,4345,4126,3536,3758,4501,4518,4574,4571,4582,3755,3441,3800,3746,3796,3778,3904,3855,3851,3894,3900,3974,3953,4016,4050,4084,4133,4322,4432,4587,3512,3873,3618,4072,4441,4426,4526,4541,4470,3930,4313,4334,4535,3502,4580,4442,3557,3966,3338,3363,3331,3334,3422,3456,4005,3998,4001,3349,3527,4464,4497,4467,4514,4567,3368,4274,4298,4428,4440,4496,3587,3336,3444,2564,3256,3347,4439,4472,4446,4412,4043,4455,4481,4451,2503,3453,3722,3373,3250,3329,3405,2885,2908,3399,3436,3407,3150,3439,4221,3191,3592,4074,4355,4384,3866,2961,2295,2299,3609,4150,3744,3068,3395,3491,3605,2883,2924,3579,3963,4323,4082,3706,3703,3098,2050,3072,3482,3091,3092,4140,3425,3520,4336,1247,2348,1861,1864,1866,1859,1863,4498,4097,4435,3899,3705,3888,3683,3365,3668,3652,4381,4383,2966,2849,3073,2999,3498,3477,4135,3964,4247,3946,4087,4143,4145,4249,4238,4308,4090,4089,3459,3374,4491,4521,4552,3446,4500,4128,4494,3251,2902,4256,3404,2709,3353];
        $percentage20 = [3631,4474,2960,2878,2899,3076,2877,3062,2949,4358,3176,3429,3869,3432,4070,4386,4458,3685,2559,3595,4260,3843,4159,4215,4217,4310,4356,4363,4368,4463,4461,4465,4473,4566,4568,4588,4294,3715,4006,4002,4265,4370,3682,4190,4194,4199,4352,4367,4357,4364,4360,4372,4397,4427,4429,4476,4502,4520,4508,4546,4550,4554,4572,4563,4575,4583,4346,4338,4031,4555,4405,4456,4531,4168,2773,3599,3612,3600,3633,3745,3603,3601,3628,2907,2886,4590,3994,3378,3664,4289,4543,4544,4545,3201,2757,4136,2891,3767,3826,4398,4577,4402,4433,4438,4434,4424,4422,4447,2896,2342,2974,3528,3839,3533,3414,2780,2792,2895,2809,2798,2962,3102,3526,3845,2627,2671,2791,2793,2894,2976,3701,3844,4137,4234,4226,4329,2624,3083,3097,3546,4026,4320,3532,3741,4471,4131,4185,3667,3836,4037,4186,4309,2768,2880,4553,2953,4227,4452,4430,4559,4565,2946,4418,4387,4403,4444,4445,4416,4423,4344,1511,4197,4263,2783,2772,3568,4214,4517,4548,4537,4561,4573,4570,4581,3670,4557,3655,3863,4054,4032,3940,4014,4183,4242,4388,4396,4409,4413,4431,4586,4085,3763,3993,3783,4231,2811,2801,2876,3130,3691,3751,4028,4011,2997,2916,3742,3046,3049,4177,4239,4350,4425,4488,4525,3563,4450,4469,3475,4230,4312,4333,4395,4534,4380,4378,2480,3878,4579,4018,2794,3361];


        // 50 percent
        SpecificDiscountItem::create([
            'specific_discount_type_id' => 6,
            'type' => 'product',
            'model_ids' => implode(',', $percentage50),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // 40 percent
        SpecificDiscountItem::create([
            'specific_discount_type_id' => 7,
            'type' => 'product',
            'model_ids' => implode(',', $percentage40),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // 30 percent
        SpecificDiscountItem::create([
            'specific_discount_type_id' => 8,
            'type' => 'product',
            'model_ids' => implode(',', $percentage30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 20 percent
        SpecificDiscountItem::create([
            'specific_discount_type_id' => 9,
            'type' => 'product',
            'model_ids' => implode(',', $percentage20),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        dd('done');

        $products50 = Product::find($percentage50);
        dump('in 50');
        dump('count 50:' . count($percentage50));
        dump('count products50:' . $products50->count());
        foreach ($products50 as $product) {
            $test = $product->title;
        }
        $products40 = Product::find($percentage40);
        dump('in 40');
        dump('count 40:' . count($percentage40));
        dump('count products40:' . $products40->count());
        foreach ($products40 as $product) {
            $test = $product->title;
        }
        $products30 = Product::find($percentage30);
        dump('in 30');
        dump('count 30:' . count($percentage30));
        dump('count products30:' . $products30->count());
        foreach ($products30 as $product) {
            $test = $product->title;
        }

        $products20 = Product::find($percentage20);
        dump('in 20');
        dump('count 20:' . count($percentage20));
        dump('count products20:' . $products20->count());
        foreach ($products20 as $product) {
            $test = $product->title;
        }
        dd('HERE');



        dd(now()->format('Y-m-d H:i:s'));
        return (new ReportController())->productsBalance();
        dd('HERE');
    }

    public function add()
    {
        // Schema::create('specific_discounts', function (Blueprint $table) {
        //     $table->id();

        //     $table->string('title');
        //     $table->timestamp('start_date');
        //     $table->timestamp('end_date');
        //     $table->timestamp('done_at')->nullable();
        //     $table->foreignId('creator_id')->constrained('admins')->restrictOnDelete();
        //     $table->foreignId('updater_id')->constrained('admins')->restrictOnDelete();

        //     $table->timestamps();
        // });

        // Schema::create('specific_discount_types', function (Blueprint $table) {
        //     $table->id();

        //     $table->foreignId('specific_discount_id')->constrained('specific_discounts')->cascadeOnDelete();
        //     $table->enum('discount_type', [Product::DISCOUNT_TYPE_PERCENTAGE, Product::DISCOUNT_TYPE_FLAT])->nullable();
        //     $table->unsignedInteger('discount')->nullable();

        //     $table->timestamps();
        // });

        // Schema::create('specific_discount_items', function (Blueprint $table) {
        //     $table->id();

        //     $table->foreignId('specific_discount_type_id')->constrained('specific_discount_types')->cascadeOnDelete();
        //     $table->enum('type', [SpecificDiscountItem::TYPE_CATEGORY,SpecificDiscountItem::TYPE_PRODUCT,SpecificDiscountItem::TYPE_BALANCE,SpecificDiscountItem::TYPE_RANGE]);

        //     $table->text('model_ids')->nullable();
        //     $table->string('balance')->nullable();
        //     $table->enum('balance_type', [SpecificDiscountItem::BALANCE_TYPE_LESS, SpecificDiscountItem::BALANCE_TYPE_MORE])->nullable();
        //     $table->unsignedInteger('range_from')->nullable();
        //     $table->unsignedInteger('range_to')->nullable();

        //     $table->timestamps();
        // });

        // dd('MIGRATION DONE');
        // Schema::create('variety_transfer_locations', function (Blueprint $table) {
        //     $table->id();

        //     $table->string('title');
        //     $table->boolean('is_delete');

        //     $table->timestamps();
        // });
        // Schema::create('variety_transfers', function (Blueprint $table) {

        //     $table->id();

        //     $table->unsignedInteger('quantity');
        //     $table->text('description')->nullable();
        //     $table->string('from');
        //     $table->string('to');
        //     $table->string('mover');
        //     $table->string('receiver');

        //     $table->foreignId('creator_id')->constrained('admins')->restrictOnDelete();
        //     $table->boolean('is_delete')->default(false);

        //     $table->foreignId('from_location_id')->nullable()->constrained('variety_transfer_locations')->restrictOnDelete();
        //     $table->foreignId('to_location_id')->nullable()->constrained('variety_transfer_locations')->restrictOnDelete();

        //     $table->timestamps();
        // });
        // Schema::create('variety_transfer_items', function (Blueprint $table) {
        //         $table->id();

        //         $table->foreignId('variety_transfer_id')->constrained('variety_transfers')->cascadeOnDelete();
        //         $table->foreignId('variety_id')->constrained('varieties')->restrictOnDelete();
        //         $table->unsignedInteger('quantity');

        //         $table->timestamps();
        //     });
            // Schema::table('variety_transfers', function (Blueprint $table) {
            //     $table->string('receiver');
            // });
        }
    }
