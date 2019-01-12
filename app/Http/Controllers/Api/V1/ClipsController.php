<?php

namespace App\Http\Controllers\Api\V1;

use App\Clip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClipsRequest;
use App\Http\Requests\Admin\UpdateClipsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use \Response;

use GuzzleHttp\Client as Guzzle;
use Cache;

class ClipsController extends Controller
{
    use FileUploadTrait;

    /**
     * @var        Guzzle    $guzzle    Property description.
     */
    protected $guzzle;

    /**
     * Set the dependencies.
     *
     *  https://laracasts.com/series/whatcha-working-on/episodes/7
     *
     * @param    Guzzle    $guzzle
     * @return    void
     */
    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }


    public function index()
    {
        //$clips = Clip::all();

        //$feed = $this->fetchFeed();

        return Clip::all();
    }


    protected function fetchFeed()
    {
        //make api request
        // return Cache::remember(60*24*7, 'clips', function()
        // {
            // $endpoint = 'https://adamsurl.com/clips'. config('services.dishrest.key');
            // https://reqres.in/

           //  $payload = [
           //      "token" => "6Ykf27J_BT8yG_aJy_u9Lg",
           //      "data" => [
           //        "name"=> "nameFirst",
           //        "email"=> "internetEmail",
           //        "phone"=> "phoneHome",
           //        "_repeat"=> 300
           //      ]
           // ];


            // $endpoint = 'https://reqres.in/api/users';

            // json_decode($this->guzzle->get($endpoint)->getBody());
       // });

        // cache response
    }

    public function show($id)
    {
        return Clip::findOrFail($id);
    }

    public function update(UpdateClipsRequest $request, $id)
    {
        $request = $this->saveFiles($request);
        $clip = Clip::findOrFail($id);
        $clip->update($request->all());


        return $clip;
    }

    public function store(StoreClipsRequest $request)
    {
        $request = $this->saveFiles($request);
        $clip = Clip::create($request->all());


        return $clip;
    }

    public function destroy($id)
    {
        $clip = Clip::findOrFail($id);
        $clip->delete();
        return '';
    }
}
