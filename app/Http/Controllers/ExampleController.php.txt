<?php

namespace App\Http\Controllers;

use App\BlogPost;
use App\PhotoAlbum;
use Illuminate\Support\Collection;
use League\CommonMark\CommonMarkConverter;
use File;

class ExampleController extends Controller
{
    public function example(string $example)
    {
        $this->checkIfExampleExists($example);

        $model = $this->getModel($example);
        $media = $this->getMedia($example);
        $title = ucfirst(kebab_case_to_string($example));
        $content = (new CommonMarkConverter())
            ->convertToHtml(File::get(resource_path("views/{$example}.md")));

        return view('example', compact('title', 'content', 'model', 'media'));
    }

    protected function checkIfExampleExists(string $example)
    {
        if (! File::exists(resource_path("views/{$example}.md"))) abort(404);

        return;
    }

    protected function getMedia(string $example): Collection
    {
        switch ($example) {
            case 'basic-usage':
                return PhotoAlbum::first()->getMedia();
                break;
            case 'converting-images':
            case 'converting-other-files':
                return BlogPost::first()->getMedia();
                break;
            default:
                return new Collection();
        }
    }

    protected function getModel(string $example): string
    {
        switch ($example) {
            case 'basic-usage':
                return 'photoalbum';
                break;
            case 'converting-images':
            case 'converting-other-files':
                return 'blogpost';
                break;
            default:
                return '';
        }
    }
}
