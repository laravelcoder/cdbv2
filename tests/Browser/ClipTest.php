<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ClipTest extends DuskTestCase
{

    public function testCreateClip()
    {
        $admin = \App\User::find(1);
        $clip = factory('App\Clip')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $clip) {
            $browser->loginAs($admin)
                ->visit(route('admin.clips.index'))
                ->clickLink('Add new')

                ->press('Save')
                ->assertRouteIs('admin.clips.index')

                ->logout();
        });
    }

    public function testEditClip()
    {
        $admin = \App\User::find(1);
        $clip = factory('App\Clip')->create();
        $clip2 = factory('App\Clip')->make();

        

        $this->browse(function (Browser $browser) use ($admin, $clip, $clip2) {
            $browser->loginAs($admin)
                ->visit(route('admin.clips.index'))
                ->click('tr[data-entry-id="' . $clip->id . '"] .btn-info')

                ->press('Update')
                ->assertRouteIs('admin.clips.index')

                ->logout();
        });
    }

    public function testShowClip()
    {
        $admin = \App\User::find(1);
        $clip = factory('App\Clip')->create();

        


        $this->browse(function (Browser $browser) use ($admin, $clip) {
            $browser->loginAs($admin)
                ->visit(route('admin.clips.index'))
                ->click('tr[data-entry-id="' . $clip->id . '"] .btn-primary')

                ->logout();
        });
    }

}
