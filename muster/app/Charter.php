<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Charter extends Model {

  protected $guarded = ['id'];

  protected $dates = ['approved_at', 'approval_requested_at', 'active_from'];

  public function league()
  {
    return $this->belongsTo('App\League');
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
    return env('APP_URL') . '/leagues/' . $this->league->slug . '/charters/' . $this->slug;
  }
}
