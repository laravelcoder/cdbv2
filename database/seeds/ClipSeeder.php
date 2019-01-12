<?php

use Illuminate\Database\Seeder;
use App\Clip;


class ClipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
public function run()
    {
        // Let's truncate our existing records to start from scratch.
        Clip::truncate();

        $faker = \Faker\Factory::create();

        // And now, let's create a few articles in our database:
        for ($i = 0; $i < 50; $i++) {
            $date = $faker->iso8601($max = 'now');
            $url = $faker->url;

            Clip::create([
                // 'videos' => 'fakevideo.mp4',
                // 'images' => $faker->imageUrl($width = 368, $height = 232)
                //,
                // 'title' => $faker->sentence,
                // 'body' => $faker->paragraph,
                // 'asset' => 'gui_00000001_' . $faker->unixTime($max = 'now'),
                // 'agency' => $faker->company,
                // 'displayasset' => $faker->word,
                // 'sourceurl' => $url,
                // 'imagedirurl' => $url,
                // 'caipyurl' => $url,
                // 'metadata' => $faker->word,
                // 'CID' => 'gui_00000001_' . $faker->unixTime($max = 'now'),
                // 'ISCI_Ad-ID' => null,
                // 'CopyLength' => '00:00:30',
                // 'Industry' => $faker->word,
                // 'Advertiser' => $faker->word,
                // 'Brand' => $faker->word,
                // 'Product' => $faker->word,
                // 'Location' => $faker->word,
                // 'Media-Content' => $faker->word,
                // 'Media-CAI' => $faker->word,
                // 'Media-MP4' => $faker->word,
                // 'Media-Default-IMG' => $faker->word,
                // 'Media-IMG' => $faker->word,
                // 'Media-IMG-results' => $faker->word,
                // 'ScheduleDate' => $date,
                // 'ExpirationDate' => $date,
                // 'family' => $faker->word,
                // 'subfamily' => $faker->word,
                // 'group' => $faker->word,
                // 'Caipy_clipIds' => 'gui_00000001_' . $faker->unixTime($max = 'now'),
                // 'created_epoch' => $date,
                // 'modified_epoch' => $date,
                'converted_for_downloading_at' => $date,
                'converted_for_streamig_at' => $date,
                'converted_to_cai' => $date
                // 'synced' => $date,
                // 'creative_airing_date_first' => $date,
                // 'creative_airing_date_last' => $date,
                // 'campaignId' => null,
                // 'duration' => '00:00:30',
                // 'lifetimeAirings' => str_random(3),
                // 'optimalFrequency' => '5',
                // 'reportDate' => $date,
                // 'reportWindow' => '90',
                // 'totalImpressions' => str_random(8),
                // 'totalMatchedConversions' => str_random(4),
                // 'reviewState' => '1',
                // 'ignoreimport' => '1',
            ]);
        }
    }
}
