<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Charter extends Model {

  protected $guarded = ['id'];

  protected $dates = ['approved_at', 'approval_requested_at', 'active_from'];

  public function league()
  {
    return $this->belongsTo('App\League');
  }

  public function charter_type()
  {
    return $this->belongsTo('App\CharterType');
  }

  public function skaters()
  {
    return $this->hasMany('App\Skater')->limit(20)->orderBy('number');
  }

  public function replaceSkaters( array $skaters )
  {
    if( count( $this->skaters ) )
    {
      foreach( $this->skaters as $skater )
      {
        $skater->delete();
      }
    }

    if( count( $skaters ) )
    {
      foreach( $skaters as $skater )
      {
        Skater::create( array_merge( $skater, array('charter_id' => $this->id ) ) );
      }
    }
  }

  public function canonicalUrl()
  {
    return $this->league->canonicalUrl() . '/charters/' . $this->slug;
  }

  public function types()
  {
    $charter_types = array();
    foreach( \App\CharterType::all() as $charter_type )
    {
      $charter_types[ $charter_type->id ] = $charter_type->name;
    }
    return $charter_types;
  }
}
