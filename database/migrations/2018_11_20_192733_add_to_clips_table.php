<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToClipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clips', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->unsignedInteger('percentage')->nullable();
            $table->unsignedInteger('creator')->nullable();
            $table->datetime('converted_for_downloading_at')->nullable();
            $table->datetime('converted_for_streaming_at')->nullable();
            $table->datetime('sync_rest_to_clip')->nullable();
                $table->datetime('format_the_mp4')->nullable();
                    $table->datetime('normalized_the_mp4')->nullable();
                        $table->datetime('generated_pngs')->nullable();
                            $table->datetime('converted_to_cai')->nullable();
                                $table->datetime('fileinfo_created_at')->nullable();
                                    $table->datetime('sync_clip_to_rest')->nullable();

        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clips', function (Blueprint $table) {
            //
        });
    }
}
