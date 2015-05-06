<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class League extends Model {

  protected $guarded = ['id'];

  public function user()
  {
    return $this->belongsTo('App\User');
  }

  public function charters( $type_id = null )
  {
    $charters = $this->hasMany('App\Charter')->limit(20);
    if( $type_id )
    {
      $charters->where('charter_type_id', '=', $type_id );
    }
    return $charters;
  }

  public function approvedCharters( $type_id = null )
  {
    return $this->charters( $type_id )->whereNotNull('active_from')->orderBy('active_from', 'desc');
  }

  public function currentCharter( $type_id = null )
  {
    return $this->approvedCharters( $type_id )->where('active_from', '<=', Carbon::now() )->first();
  }

  public function draftCharter( $type_id = null )
  {
    return $this->charters( $type_id )->whereNull('active_from')->whereNull('approval_requested_at')->first();
  }

  public function historicalCharters( $type_id = null )
  {
    return $this->approvedCharters( $type_id )->where('active_from', '<=', Carbon::now() )->take(10)->skip(1)->get();
  }

  public function pendingCharter( $type_id = null )
  {
    return $this->charters( $type_id )->whereNull('active_from')->whereNotNull('approval_requested_at')->first();
  }

  public function upcomingCharter( $type_id = null )
  {
    return $this->approvedCharters( $type_id )->where('active_from', '>', Carbon::now() )->first();
  }

  public function usersUpForGrabs()
  {
    $leagues = DB::table('leagues')->whereNotNull('user_id')->select('user_id')->get();
    $user_ids = array();
    foreach( $leagues as $league )
    {
      $user_ids[] = $league->user_id;
    }

    $users = array( 0 => '- none -');

    $query = DB::table('users')->whereNotIn('id', $user_ids );
    if( !is_null( $this->user_id ) )
    {
      $query->orWhere('id', '=', $this->user_id );
    }

    $records = $query->orderBy('name', 'asc')->get();

    foreach( $records as $user )
    {
      $users[ $user->id ] = $user->name;
    }
    ksort( $users );
    return $users;
  }
}