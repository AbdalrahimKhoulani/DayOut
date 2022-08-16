<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class User extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'password',
        'photo',
        'gender',
        'email',
        'mobile_token'
    ];

    protected $casts = [
        'verified_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'pivot',
        'deleted_at',
        'is_active'
    ];

    public function isAdmin(){

        $adminRole = Role::where('name','=','admin')->first();

        //  $b = in_array($adminRole,$user->roles );

        $isAdmin= false;
        foreach (Auth::user()->roles as $role) {
            if($adminRole->id==$role->id)
            $isAdmin = true;
        }

        return $isAdmin;
    }

    public function isOrganizer(){


        $organizerRole = Role::where('name','=','organizer')->first();

        $isOrganizer= false;
        foreach (Auth::user()->roles as $role) {
            if($organizerRole->id==$role->id)
                $isOrganizer = true;
        }

        return $isOrganizer;
    }

    public function verifiedAccount()
    {
        return $this->forceFill(['verified_at' => $this->freshTimestamp()])->save();
    }

    public function confirmCode()
    {
        return $this->hasOne(ConfirmationCode::class, 'user_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    public function targets()
    {
        return $this->hasMany(UserReport::class, 'target_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function promotionRequest()
    {
        return $this->hasMany(PromotionRequest::class, 'user_id', 'id');
    }

    public function organizer()
    {
        return $this->hasOne(Organizer::class, 'user_id', 'id');
    }

    public function organizerFollow()
    {
        return $this->belongsToMany(Organizer::class, 'followers', 'user_id', 'organizer_id')->withTimestamps();
    }

    public function pollChoices()
    {
        return $this->belongsToMany(PollChoice::class, 'customer_poll_choices', 'user_id', 'poll_choice_id');
    }

    public function polls()
    {
        return $this->belongsToMany(Poll::class, 'customer_poll_choices', 'user_id', 'poll_id');
    }

    public function favoritePlace()
    {
        return $this->belongsToMany(Place::class, 'favorite_places', 'user_id', 'place_id');
    }

    public function customerTrip()
    {
        return $this->belongsToMany(Trip::class, 'customer_trips', 'customer_id', 'trip_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id','id');
    }
}
