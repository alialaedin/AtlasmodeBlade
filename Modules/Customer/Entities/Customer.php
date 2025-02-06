<?php

namespace Modules\Customer\Entities;

use Illuminate\Foundation\Auth\User;
use Bavix\Wallet\Traits\CanPay;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Traits\HasPushTokens;
use Modules\Cart\Entities\Cart;
use Modules\Core\Classes\CoreSettings;
use Modules\Core\Contracts\Notifiable;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Core\Helpers\Helpers;
use Modules\Core\Traits\HasAuthors;
use Bavix\Wallet\Interfaces\Customer as CustomerWallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable as NotificationsNotifiable;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\InteractsWithMedia;
use Modules\Core\Transformers\MediaResource;
use Modules\Coupon\Entities\Coupon;
use Modules\Notification\Entities\Notification;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\ListenCharge;
use Modules\Product\Entities\Product;
use Modules\ProductComment\Entities\ProductComment;
use Shetabit\Shopit\Modules\Core\Entities\BaseModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Modules\Customer\Entities\Address;
use Modules\Order\Entities\MiniOrder;

class Customer extends User implements CustomerWallet, Notifiable, HasMedia
{
    use BaseModelTrait, InteractsWithMedia, HasAuthors, CanPay, NotificationsNotifiable, HasPushTokens;

    const MALE = 'male';
    const FEMALE = 'female';

    const NOTIFICATION_FIELDS = ['id', 'read_at', 'type', 'data', 'created_at'];

    protected $with = ['wallet'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'mobile',
        'national_code',
        'gender',
        'card_number',
        'birth_date',
        'newsletter',
        'foreign_national',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'media',
        'updater'
    ];

    protected $appends = [
        'full_name',
        'image',
        'last_online_order_date',
        'last_real_order_date',
        'persian_register_date',
    ];

    public function __construct(array $attributes = [])
    {
        $coreSetting = app(CoreSettings::class);
        if ($coreSetting->get('customer.has_role')) {
            $this->with = array_merge($this->with, ['role']);
            $this->mergeFillable(['role_id']);
        }
        parent::__construct($attributes);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function booted()
    {
        static::deleting(function (Customer $customer) {
            if ($customer->orders()->count()) {
                throw new ModelCannotBeDeletedException('مشتری به دلیل داشتن سفارش قابل حذف نمی باشد');
            }
        });
        static::deleted(function (\Modules\Customer\Entities\Customer $customer) {
            $smsToken = \Modules\Customer\Entities\SmsToken::query()->firstWhere('mobile', $customer->mobile);
            $smsToken?->delete();
        });
    }

    public static function getAvailableGenders()
    {
        return [static::MALE, static::FEMALE];
    }

    public function getFullNameAttribute(): string
    {
        if (!$this->first_name && !$this->last_name) {
            return '';
        }
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getGiftBalanceAttributes()
    {
        return DB::table('wallets')
            ->where('holder_type', 'Modules\Customer\Entities\Customer')
            ->where('holder_id', $this->id)
            ->latest('id')
            ->first()->gift_balance;
    }

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = null;
        }
    }

    /**
     * @throws ValidationException
     */
    public function verify(string $mobile, string $token)
    {
        $smsToken = SmsToken::where('mobile', $mobile)->first();

        if (!$smsToken || $smsToken->token !== $token) {
            throw ValidationException::withMessages([
                'sms_token' => 'کد وارد شده نادرست است.'
            ]);
        }

        if (Carbon::now()->diffInMinutes($smsToken->updated_at) > 5) {
            throw ValidationException::withMessages([
                'sms_token' => 'کد وارد شده منقضی شده است.'
            ]);
        }

        //update verified_at
        $smsToken->verified_at = now();
        $smsToken->save();
    }

    //Relations

    public function role()
    {
        return $this
            ->belongsTo(
                \Modules\Customer\Entities\CustomerRole::class,
                'role_id'
            );
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function productComments(): HasMany
    {
        return $this->hasMany(ProductComment::class, 'creator_id');
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites')->with('varieties');
    }

    public function listenCharges()
    {
        return $this->hasMany(ListenCharge::class);
    }

    public function deposits()
    {
        return $this->hasMany(\Modules\Customer\Entities\Deposit::class);
    }

    public function smsTokens()
    {
        return $this->hasOne(
            \Modules\Customer\Entities\SmsToken::class,
            'mobile',
            'mobile'
        );
    }

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest('created_at');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
    }

    public function addImage($file)
    {
        if (!$file) return;
        $media = $this->addMediaFromBase64($file)
            ->toMediaCollection('images');
        $this->load('media');

        return new MediaResource($media);
    }

    public function getImageAttribute()
    {
        $media = $this->getFirstMedia('images');
        if (!$media) return;

        return new MediaResource($media);
    }

    public function prepareForExcel()
    {
        $this->makeHidden('image');
    }

    public function canSeeUnpublishedProducts()
    {
        if (!$this->role_id) {
            return false;
        }
        $role = $this->role;

        return $role && $role->see_expired;
    }

    public function removeEmptyCarts()
    {
        $carts = $this->carts()->get();
        foreach ($carts as $cart) {
            if ($cart->quantity == 0) {
                $cart->delete();
            }
        }
        $this->load('carts');
    }

    public function isActive()
    {
        // هیچوقت نال نیست ولی برای ساپورت پروژه های قبلی
        return $this->status === null || $this->status === 1;
    }


    public function miniOrders()
    {
        return $this->hasMany(MiniOrder::class);
    }

    // total_amount
    public function getTotalMoneySpentAttribute()
    {
        return $this->orders()->whereStatus(Order::STATUS_DELIVERED)->get()->sum('total_amount');
    }

    public function boughtProducts()
    {
        return Product::query()
            ->whereHas('orderItems.order', fn(Builder $query) => $query->whereCustomerId($this->id)->whereStatus(Order::STATUS_DELIVERED))->get();
    }

    public function getLastOnlineOrderDateAttribute()
    {
        $last_order = $this->orders()->where('status', Order::STATUS_DELIVERED)->latest('id')->value('created_at');
        return $last_order ? (new Helpers)->convertMiladiToShamsi($last_order) : null;
    }

    public function getLastRealOrderDateAttribute()
    {
        $last_mini_order = $this->miniOrders()->latest('id')->value('created_at');
        return $last_mini_order ? (new Helpers)->convertMiladiToShamsi($last_mini_order) : null;
    }

    public function getPersianRegisterDateAttribute()
    {
        return (new Helpers)->convertMiladiToShamsi($this->created_at);
    }

    public function scopeFilters($query)
    {
        return $query
            ->when(request('first_name'), function ($query) {
                $query->where('first_name', 'like', "%" . request('first_name') . "%");
            })
            ->when(request('last_name'), function ($query) {
                $query->where('last_name', 'like', "%" . request('last_name') . "%");
            })
            ->when(request('mobile'), function ($query) {
                $query->where('mobile', request('mobile'));
            })
            ->when(request('id'), function ($query) {
                $query->where('id', request('id'));
            })
            ->when(request('has_deposits'), function ($query) {
                $query->whereHas('deposits', fn($query) => $query->where('status', 'success'));
            })
            ->when(request('has_transactions'), function ($query) {
                $query->whereHas('transactions');
            })
            ->when(request('start_date'), function ($query) {
                $query->whereDate('created_at', '>=', request('start_date'));
            })
            ->when(request('end_date'), function ($query) {
                $query->whereDate('created_at', '<=', request('end_date'));
            })
            ->when(request('city_id') || request('province_id'), function ($q) {
                $q->whereHas('addresses', function ($q) {
                    if (request('city_id')) {
                        $q->where('city_id', request('city_id'));
                    }
                    if (request('province_id')) {
                        $q->orWhereHas('city', function ($q) {
                            $q->where('province_id', request('province_id'));
                        });
                    }
                });
            });
    }
}
